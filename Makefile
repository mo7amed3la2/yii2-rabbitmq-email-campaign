#------------------------------------------------------------------------------
# COMMANDS
#------------------------------------------------------------------------------
init_composer:
	@$(call PRINT_INFO, "Установка глобальной зависимости")
	@docker exec -it yii2rabbitmqtest_php_1 composer self-update
	@docker exec -it yii2rabbitmqtest_php_1 composer global require "fxp/composer-asset-plugin:^1.3.1"
update:
	@$(call PRINT_INFO, "Обновление зависимостей")
	@docker exec -it yii2rabbitmqtest_php_1 composer update

publish:
	@$(call PRINT_INFO, "Yii: Публикация")
	@docker exec -it --user 1000 yii2rabbitmqtest_php_1 php yii send-msg/publish

consume:
	@$(call PRINT_INFO, "Yii: Обработка")
	@docker exec -it --user 1000 yii2rabbitmqtest_php_1 php yii rabbitmq-consumer/multiple import_data

#------------------------------------------------------------------------------
# VARS
#------------------------------------------------------------------------------
define PRINT_INFO
	echo -e "\033[1;48;5;33m$1 \033[0m"
endef