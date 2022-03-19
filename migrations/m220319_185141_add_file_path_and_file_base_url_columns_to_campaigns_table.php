<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%campaigns}}`.
 */
class m220319_185141_add_file_path_and_file_base_url_columns_to_campaigns_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('campaigns', 'file_path', $this->string() . ' AFTER `body`');
        $this->addColumn('campaigns', 'file_base_url', $this->string() . ' AFTER `file_path`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('campaigns', 'file_path');
        $this->dropColumn('campaigns', 'file_base_url');
    }
}
