# Yii2 with Rabbitmq for Simulate Larage Email Campaign

After adding a new campaign make sure to run the next two commands to subscribe for added new data in queues.

This command `./yii  rabbitmq/consume consumer-campaign` listening to a new campaign has been addedd to queue .

This command ` ./yii  rabbitmq/consume consumer-email` listening to a new send email has been addedd to queue .

You can simulate running multiple services via opening multiple terminals' tabs and listening to each command more than one time.
Then rabbitMQ will distribute the load on all opening terminal connections.

# Note

1. Change Database connection data in config/db.php
3. Change rabbitMQ connection data in config/rabbitmq.php
4. Change  Mail Connection data in config/console.php and config/web.php
