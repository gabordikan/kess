
<?php

use yii\db\Migration;

/**
 * Class m230603_132900_deviza_hozzaadasa_penztarcahoz
 */
class m230728_134200_megjegyzes_hozzaadasa_mozgashoz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `mozgas` 
        add column `megjegyzes` varchar(300) NOT NULL DEFAULT '' after osszeg")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
            ALTER TABLE `mozgas` drop column megjegyzes
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
