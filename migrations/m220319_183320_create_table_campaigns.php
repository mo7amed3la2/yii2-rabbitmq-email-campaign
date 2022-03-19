<?php

use yii\db\Migration;

/**
 * Class m220319_183320_create_table_campaigns
 */
class m220319_183320_create_table_campaigns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('campaigns', [
            'id' => $this->primaryKey(),
            'subject' => $this->string()->notNull(),
            'body' => $this->text()->notNull(),
            'created_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('compaigns');
    }
}
