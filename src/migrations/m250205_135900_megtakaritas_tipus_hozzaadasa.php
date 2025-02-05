
<?php

use yii\db\Migration;

/**
 * Class m241226_140500_csoport_kod_hozzaadasa
 */
class m250205_135900_megtakaritas_tipus_hozzaadasa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `penztarca` 
        add column `megtakaritas` tinyint NOT NULL DEFAULT 0 after deviza")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
            ALTER TABLE `penztarca` drop column megtakaritas
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
