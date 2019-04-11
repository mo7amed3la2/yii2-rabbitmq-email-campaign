#------------------------------------------------------------------------------
# COMMANDS
#------------------------------------------------------------------------------
init_composer:
	@$(call PRINT_INFO, "Global dependencies installation")
	@docker exec -it yii2-rabbitmq-test_php_1 composer self-update
	@docker exec -it yii2-rabbitmq-test_php_1 composer global require "fxp/composer-asset-plugin:^1.3.1"

install:
	@$(call PRINT_INFO, "Dependencies installation")
	@docker exec -it yii2-rabbitmq-test_php_1 composer install

update:
	@$(call PRINT_INFO, "Dependencies update")
	@docker exec -it yii2-rabbitmq-test_php_1 composer update

publish:
	@$(call PRINT_INFO, "Yii: Publish to import queue")
	@docker exec -it --user 1000 yii2-rabbitmq-test_php_1 php yii send-msg/publish

consume:
	@$(call PRINT_INFO, "Yii: Consume import queue")
	@docker exec -it --user 1000 yii2-rabbitmq-test_php_1 php yii rabbitmq/consume import

#------------------------------------------------------------------------------
# VARS
#------------------------------------------------------------------------------
define PRINT_INFO
	echo -e "\033[1;48;5;33m$1 \033[0m"
endef
