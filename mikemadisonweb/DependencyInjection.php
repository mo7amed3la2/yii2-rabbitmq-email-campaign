<?php

namespace mikemadisonweb\rabbitmq;

use mikemadisonweb\rabbitmq\components\{
    AbstractConnectionFactory, Consumer, ConsumerInterface, Producer, Routing
};
use mikemadisonweb\rabbitmq\exceptions\InvalidConfigException;
use PhpAmqpLib\Connection\AbstractConnection;
use yii\base\Application;
use yii\base\BootstrapInterface;

class DependencyInjection implements BootstrapInterface
{
    /**
     * Configuration auto-loading
     * @param Application $app
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        $config = $app->rabbitmq->getConfig();
        $this->registerConnections($config);
        $this->registerRouting($config);
        $this->registerProducers($config);
        $this->registerConsumers($config);
    }

    /**
     * Set connections to service container
     * @param Configuration $config
     */
    protected function registerConnections(Configuration $config)
    {
        foreach ($config->connections as $options) {
            $serviceAlias = sprintf(Configuration::CONNECTION_SERVICE_NAME, $options['name']);
            \Yii::$container->setSingleton($serviceAlias, function () use ($options) {
                $factory = new AbstractConnectionFactory($options['type'], $options);
                return $factory->createConnection();
            });
        }
    }

    /**
     * Set routing to service container
     * @param Configuration $config
     */
    protected function registerRouting(Configuration $config)
    {
        \Yii::$container->setSingleton(Configuration::ROUTING_SERVICE_NAME, function () use ($config) {
            $routing = new Routing();
            \Yii::$container->invoke([$routing, 'setQueues'], [$config->queues]);
            \Yii::$container->invoke([$routing, 'setExchanges'], [$config->exchanges]);
            \Yii::$container->invoke([$routing, 'setBindings'], [$config->bindings]);

            return $routing;
        });
    }

    /**
     * Set producers to service container
     * @param Configuration $config
     */
    protected function registerProducers(Configuration $config)
    {
        $autoDeclare = $config->autoDeclare;
        $logger = $config->logger;
        foreach ($config->producers as $options) {
            $serviceAlias = sprintf(Configuration::PRODUCER_SERVICE_NAME, $options['name']);
            \Yii::$container->setSingleton($serviceAlias, function () use ($options, $autoDeclare, $logger) {
                /**
                 * @var $connection AbstractConnection
                 */
                $connection = \Yii::$container->get(sprintf(Configuration::CONNECTION_SERVICE_NAME, $options['connection']));
                $producer = new Producer($connection, $autoDeclare);
                \Yii::$container->invoke([$producer, 'setContentType'], [$options['contentType']]);
                \Yii::$container->invoke([$producer, 'setDeliveryMode'], [$options['deliveryMode']]);
                \Yii::$container->invoke([$producer, 'setSerializer'], [$options['serializer']]);
                \Yii::$container->invoke([$producer, 'setLogger'], [$logger]);

                return $producer;
            });
        }
    }

    /**
     * Set consumers(one instance per multiple queues) to service container
     * @param Configuration $config
     */
    protected function registerConsumers(Configuration $config)
    {
        $autoDeclare = $config->autoDeclare;
        $logger = $config->logger;
        foreach ($config->consumers as $options) {
            $serviceAlias = sprintf(Configuration::CONSUMER_SERVICE_NAME, $options['name']);
            \Yii::$container->setSingleton($serviceAlias, function () use ($options, $autoDeclare, $logger) {
                /**
                 * @var $connection AbstractConnection
                 */
                $connection = \Yii::$container->get(sprintf(Configuration::CONNECTION_SERVICE_NAME, $options['connection']));
                $consumer = new Consumer($connection, $autoDeclare);
                $queues = [];
                foreach ($options['callbacks'] as $queueName => $callback) {
                    $callbackClass = $this->getCallbackClass($callback);
                    $queues[$queueName] = [$callbackClass, 'execute'];
                }
                \Yii::$container->invoke([$consumer, 'setQueues'], [$queues]);
                if (isset($options['qos_options'])) {
                    \Yii::$container->invoke([$consumer, 'setQosOptions'], [
                        $options['qos_options']['prefetch_size'],
                        $options['qos_options']['prefetch_count'],
                        $options['qos_options']['global'],
                    ]);
                }
                if (isset($options['idle_timeout'])) {
                    \Yii::$container->invoke([$consumer, 'setIdleTimeout'], [
                        $options['idle_timeout'],
                    ]);
                }
                if (isset($options['idle_timeout_exit_code'])) {
                    \Yii::$container->invoke([$consumer, 'setIdleTimeoutExitCode'], [
                        $options['idle_timeout_exit_code'],
                    ]);
                }
                \Yii::$container->invoke([$consumer, 'setLogger'], [$logger]);

                return $consumer;
            });
        }
    }

    /**
     * @param string $callbackName
     * @return ConsumerInterface
     * @throws InvalidConfigException
     */
    private function getCallbackClass(string $callbackName)
    {
        if (!is_string($callbackName)) {
            throw new InvalidConfigException('Consumer `callback` parameter value should be a class name or service name in DI container.');
        }
        if (!class_exists($callbackName)) {
            $callbackClass = \Yii::$container->get($callbackName);
        } else {
            $callbackClass = new $callbackName();
        }
        if (!($callbackClass instanceof ConsumerInterface)) {
            throw new InvalidConfigException("{$callbackName} should implement ConsumerInterface.");
        }

        return $callbackClass;
    }
}