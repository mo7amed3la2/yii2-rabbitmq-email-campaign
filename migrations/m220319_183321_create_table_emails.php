<?php

use yii\db\Migration;

/**
 * Class m220319_183321_create_table_emails
 */
class m220319_183321_create_table_emails extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('emails', [
            'id' => $this->primaryKey(),
            'campaign_id' => $this->integer()->notNull(),
            'email' => $this->string()->notNull(),
            'is_valid' => $this->boolean()->defaultValue(0),
        ]);


        $this->createIndex(
            'idx-email-campaign_id',
            'emails',
            'campaign_id'
        );

        // add foreign key for table `campaigns`
        $this->addForeignKey(
            'fk-email-campaign_id',
            'emails',
            'campaign_id',
            'campaigns',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `campaigns`
        $this->dropForeignKey(
            'fk-email-campaign_id',
            'emails'
        );

        // drops index for column `campaign_id`
        $this->dropIndex(
            'idx-email-campaign_id',
            'emails'
        );

        $this->dropTable('emails');
    }
}
