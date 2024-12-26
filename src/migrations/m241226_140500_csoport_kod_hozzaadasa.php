
<?php

use yii\db\Migration;

/**
 * Class m241226_140500_csoport_kod_hozzaadasa
 */
class m241226_140500_csoport_kod_hozzaadasa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `kategoriak` 
        add column `csoport_kod` int NOT NULL DEFAULT 0 after technikai")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
            ALTER TABLE `kategoriak` drop column csoport_kod
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
