<?php

use yii\db\Migration;

/**
 * Class m230601_105600_technikai_kategoria
 */
class m230601_105600_technikai_kategoria extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `kategoriak` 
            add column `technikai` tinyint(1) NOT NULL DEFAULT 0 after nev")->execute();

        $this->db->createCommand("update kategoriak set technikai=1 where nev='Átvezetés'");
        $this->db->createCommand("update kategoriak set technikai=1 where nev='Nyitó'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("ALTER TABLE `kategoriak` 
            drop column `technikai`
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
