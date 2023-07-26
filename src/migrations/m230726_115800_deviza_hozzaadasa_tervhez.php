
<?php

use yii\db\Migration;

/**
 * Class m230603_132900_deviza_hozzaadasa_penztarcahoz
 */
class m230726_115800_deviza_hozzaadasa_tervhez extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE `terv` 
        add column `deviza` varchar(3) NOT NULL DEFAULT 'HUF' after osszeg")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
            ALTER TABLE `terv` drop column deviza
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
