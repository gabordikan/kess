<?php

use yii\db\Migration;

/**
 * Class m230602_103200_torolt_hozzaadasa_penztarcahoz
 */
class m230602_103200_torolt_hozzaadasa_penztarcahoz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `penztarca` 
        add column `torolt` tinyint(1) NOT NULL DEFAULT 0")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
            ALTER TABLE `penztarca` drop column torolt
        ")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230502_190015_init cannot be reverted.\n";

        return false;
    }
    */
}
