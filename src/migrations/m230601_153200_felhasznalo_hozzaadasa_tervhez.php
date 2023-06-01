<?php

use yii\db\Migration;

/**
 * Class m230601_153200_felhasznalo_hozzaadasa_tervhez
 */
class m230601_153200_felhasznalo_hozzaadasa_tervhez extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `terv` 
            add column `torolt` tinyint(1) NOT NULL DEFAULT 0, 
            add column `rogzitve` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(), 
            add column `felhasznalo` int(11) NOT NULL DEFAULT 1")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("ALTER TABLE `terv` 
            drop column `felhasznalo`,
            drop column `rogzitve`,
            drop column `torolt`,
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
