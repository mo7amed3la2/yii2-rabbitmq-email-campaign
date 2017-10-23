<?php

namespace mikemadisonweb\rabbitmq;

use mikemadisonweb\rabbitmq\exceptions\InvalidConfigException;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Configuration extends Component
{
    const CONNECTION_SERVICE_NAME = 'rabbit_mq.connection.%s';
    const CONSUMER_SERVICE_NAME = 'rabbit_mq.consumer.%s';
    const PRODUCER_SERVICE_NAME = 'rabbit_mq.producer.%s';
    const ROUTING_SERVICE_NAME = 'rabbit_mq.routing';
    const DEFAULT_CONNECTION_NAME = 'default';
    /**
     * Extension configuration default values
     * @var array
     */
    const DEFAULTS = [
        'autoDeclare' => true,
        'connections' => [
            [
                'name' => self::DEFAULT_CONNECTION_NAME,
                'type' => AMQPLazyConnection::class,
                'url' => null,
                'host' => null,
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest',
                'vhost' => '/',
                'connection_timeout' => 3,
                'read_write_timeout' => 3,
                'ssl_context' => null,
                'keepalive' => false,
                'heartbeat' => 0,
            ],
        ],
        'exchanges' => [
            [
                'name' => null,
                'type' => null,
                'passive' => false,
                'durable' => true,
                'auto_delete' => false,
                'internal' => false,
                'nowait' => false,
                'arguments' => null,
                'ticket' => null,
                'declare' => true,
            ],
        ],
        'queues' => [
            [
                'name' => '',
                'passive' => false,
                'durable' => true,
                'exclusive' => false,
                'auto_delete' => false,
                'nowait' => false,
                'arguments' => null,
                'ticket' => null,
                'declare' => true,
            ],
        ],
        'bindings' => [
            [
                'exchange' => null,
                'queue' => null,
                'toExchange' => null,
                'routingKeys' => [],
            ],
        ],
        'producers' => [
            [
                'name' => null,
                'connection' => self::DEFAULT_CONNECTION_NAME,
                'contentType' => 'text/plain',
                'deliveryMode' => 2,
                'serializer' => 'json_encode',
            ],
        ],
        'consumers' => [
            [
                'name' => null,
                'connection' => self::DEFAULT_CONNECTION_NAME,
                'callbacks' => [],
                'qos' => [
                    'prefetch_size' => 0,
                    'prefetch_count' => 0,
                    'global' => false,
                ],
                'idle_timeout' => null,
                'idle_timeout_exit_code' => null,
                'deserializer' => 'json_decode',
            ],
        ],
        'logger' => [
            'enable' => true,
            'category' => 'application',
            'print_console' => false,
            'system_memory' => false,
        ],
    ];

    public $autoDeclare = null;
    public $connections = [];
    public $producers = [];
    public $consumers = [];
    public $queues = [];
    public $exchanges = [];
    public $bindings = [];
    public $logger = [];

    protected $isLoaded = false;

    /**
     * Get passed configuration
     * @return Configuration
     * @throws InvalidConfigException
     */
    public function getConfig() : Configuration
    {
        if(!$this->isLoaded) {
            $this->normalizeConnections();
            $this->validate();
            $this->completeWithDefaults();
            $this->isLoaded = true;
        }

        return $this;
    }

    /**
     * Config validation
     * @throws InvalidConfigException
     */
    protected function validate()
    {
        $this->validateTopLevel();
        $this->validateMultidimensional();
        $this->validateRequired();
        $this->validateDuplicateNames(['connections', 'exchanges', 'queues', 'producers', 'consumers']);
    }

    /**
     * Validate multidimensional entries names
     * @throws InvalidConfigException
     */
    protected function validateMultidimensional()
    {
        $multidimensional = [
            'connection' => $this->connections,
            'exchange' => $this->exchanges,
            'queue' => $this->queues,
            'binding' => $this->bindings,
            'producer' => $this->producers,
            'consumer' => $this->consumers,
        ];

        foreach ($multidimensional as $configName => $configItem) {
            if (!is_array($configItem)) {
                throw new InvalidConfigException("Every {$configName} entry should be of type array.");
            }
            foreach ($configItem as $key => $value) {
                if (!is_int($key)) {
                    throw new InvalidConfigException("Invalid key: `{$key}`. There should be a list of {$configName}s in the array.");
                }
            }
        }
    }

    /**
     * Validate top level options
     * @throws InvalidConfigException
     */
    protected function validateTopLevel()
    {
        if (($this->autoDeclare !== null) && !is_bool($this->autoDeclare)) {
            throw new InvalidConfigException("Option `autoDeclare` should be of type boolean.");
        }

        if (!is_array($this->logger)) {
            throw new InvalidConfigException("Option `logger` should be of type array.");
        }

        $this->validateArrayFields($this->logger, self::DEFAULTS['logger']);
    }

    /**
     * Validate required options
     * @throws InvalidConfigException
     */
    protected function validateRequired()
    {
        foreach ($this->connections as $connection) {
            $this->validateArrayFields($connection, self::DEFAULTS['connections'][0]);
            if (!isset($connection['url']) && !isset($connection['host'])) {
                throw new InvalidConfigException('Either `url` or `host` options required for configuring connection.');
            }
            if (isset($connection['url']) && (isset($connection['host']) || isset($connection['port']))) {
                throw new InvalidConfigException('Connection options `url` and `host:port` should not be both specified, configuration is ambigious.');
            }
            if (!isset($connection['name'])) {
                throw new InvalidConfigException('Connection name is required when multiple connections is specified.');
            }
            if (isset($connection['type']) && !is_subclass_of(isset($connection['type']), AbstractConnection::class)) {
                throw new InvalidConfigException('Connection type should be a subclass of PhpAmqpLib\Connection\AbstractConnection.');
            }
        }

        foreach ($this->exchanges as $exchange) {
            $this->validateArrayFields($exchange, self::DEFAULTS['exchanges'][0]);
            if (!isset($exchange['name'])) {
                throw new InvalidConfigException('Exchange name should be specified.');
            }
            if (!isset($exchange['type'])) {
                throw new InvalidConfigException('Exchange type should be specified.');
            }
            $allowed = ['direct', 'topic', 'fanout', 'headers'];
            if (!in_array($exchange['type'], $allowed, true)) {
                $allowed = implode(', ', $allowed);
                throw new InvalidConfigException("Unknown exchange type `{$exchange['type']}`. Allowed values are: {$allowed}");
            }
        }
        foreach ($this->queues as $queue) {
            $this->validateArrayFields($queue, self::DEFAULTS['queues'][0]);
        }
        foreach ($this->bindings as $binding) {
            $this->validateArrayFields($binding, self::DEFAULTS['bindings'][0]);
            if (!isset($binding['exchange'])) {
                throw new InvalidConfigException('Exchange name is required for binding.');
            }
            if (!$this->isNameExist($this->exchanges, $binding['exchange'])) {
                throw new InvalidConfigException("`{$binding['exchange']}` defined in binding doesn't configured in exchanges.");
            }
            if (isset($binding['routingKeys']) && !is_array($binding['routingKeys'])) {
                throw new InvalidConfigException('Option `routingKeys` should be an array.');
            }
            if ((!isset($binding['queue']) && !isset($binding['toExchange'])) || isset($binding['queue'], $binding['toExchange'])) {
                throw new InvalidConfigException('Either `queue` or `toExchange` options should be specified to create binding.');
            }
            if (!$this->isNameExist($this->queues, $binding['queue'])) {
                throw new InvalidConfigException("`{$binding['queue']}` defined in binding doesn't configured in queues.");
            }
        }
        foreach ($this->producers as $producer) {
            $this->validateArrayFields($producer, self::DEFAULTS['producers'][0]);
            if (!isset($producer['name'])) {
                throw new InvalidConfigException('Producer name is required.');
            }

            if (isset($producer['connection']) && !$this->isNameExist($this->connections, $producer['connection'])) {
                throw new InvalidConfigException("Connection `{$producer['connection']}` defined in producer doesn't configured in connections.");
            }
            if (!isset($producer['connection']) && !$this->isNameExist($this->connections, self::DEFAULT_CONNECTION_NAME)) {
                throw new InvalidConfigException("Connection for producer `{$producer['name']}` is required.");
            }
            if (isset($producer['serializer']) && !is_callable($producer['serializer'])) {
                throw new InvalidConfigException('Producer `serializer` option should be a callable.');
            }
        }
        foreach ($this->consumers as $consumer) {
            $this->validateArrayFields($consumer, self::DEFAULTS['consumers'][0]);
            if (!isset($consumer['name'])) {
                throw new InvalidConfigException('Consumer name is required.');
            }
            if (isset($consumer['connection']) && !$this->isNameExist($this->connections, $consumer['connection'])) {
                throw new InvalidConfigException("Connection `{$consumer['connection']}` defined in consumer doesn't configured in connections.");
            }
            if (!isset($consumer['connection']) && !$this->isNameExist($this->connections, self::DEFAULT_CONNECTION_NAME)) {
                throw new InvalidConfigException("Connection for consumer `{$consumer['name']}` is required.");
            }
            if (!isset($consumer['callbacks']) || empty($consumer['callbacks'])) {
                throw new InvalidConfigException("No callbacks specified for consumer `{$consumer['name']}`.");
            }
            foreach ($consumer['callbacks'] as $queue => $callback) {
                if (!$this->isNameExist($this->queues, $queue)) {
                    throw new InvalidConfigException("Queue `{$queue}` from {$consumer['name']} is not defined in queues.");
                }
                if (!is_string($callback)) {
                    throw new InvalidConfigException('Consumer `callback` parameter value should be a class name or service name in DI container.');
                }
            }
            if (isset($consumer['deserializer']) && !is_callable($consumer['deserializer'])) {
                throw new InvalidConfigException('Consumer `deserializer` option should be a callable.');
            }
        }
    }

    /**
     * Validate config entry value
     * @param array $passed
     * @param array $required
     * @throws InvalidConfigException
     */
    protected function validateArrayFields(array $passed, array $required)
    {
        $undeclaredFields = array_diff_key($passed, $required);
        if (!empty($undeclaredFields)) {
            $asString = json_encode($undeclaredFields);
            throw new InvalidConfigException("Unknown options: {$asString}");
        }
    }

    /**
     * Check entrees for duplicate names
     * @param array $keys
     * @throws InvalidConfigException
     */
    protected function validateDuplicateNames(array $keys)
    {
        foreach ($keys as $key) {
            $names = [];
            foreach ($this->$key as $item) {
                if (isset($names[$item['name']])) {
                    throw new InvalidConfigException("Duplicate name `{$item['name']}` in {$key}");
                }
                $names[$item['name']] = true;
            }
        }
    }

    /**
     * Allow certain flexibility on connection configuration
     * @throws InvalidConfigException
     */
    protected function normalizeConnections()
    {
        if (empty($this->connections)) {
            throw new InvalidConfigException('Option `connections` should have at least one entry.');
        }
        if (ArrayHelper::isAssociative($this->connections)) {
            $this->connections[0] = $this->connections;
        }
        if (count($this->connections) === 1) {
            if (!isset($this->connections[0]['name'])) {
                $this->connections[0]['name'] = self::DEFAULT_CONNECTION_NAME;
            }
        }
    }

    /**
     * Merge passed config with extension defaults
     */
    protected function completeWithDefaults()
    {
        $defaults = self::DEFAULTS;
        if (null === $this->autoDeclare) {
            $this->autoDeclare = $defaults['autoDeclare'];
        }
        if (empty($this->logger)) {
            $this->logger = $defaults['logger'];
        } else {
            foreach ($defaults['logger'] as $key => $option) {
                if (!isset($this->logger[$key])) {
                    $this->logger[$key] = $option;
                }
            }
        }
        $multi = ['connections', 'bindings', 'exchanges', 'queues', 'producers', 'consumers'];
        foreach ($multi as $key) {
            foreach ($this->$key as &$item) {
                $item = array_replace_recursive($defaults[$key][0], $item);
            }
        }
    }

    /**
     * Check if an entry with specific name exists in array
     * @param array $multidimentional
     * @param string $name
     * @return bool
     */
    private function isNameExist(array $multidimentional, string $name)
    {
        $key = array_search($name, array_column($multidimentional, 'name'), true);
        if (is_int($key)) {
            return true;
        }

        return false;
    }
}
