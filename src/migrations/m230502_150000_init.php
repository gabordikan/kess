<?php

use yii\db\Migration;

/**
 * Class m230502_190015_init
 */
class m230502_150000_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("CREATE TABLE `penztarca` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nev` varchar(100) NOT NULL DEFAULT '',
            `rogzitve` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `felhasznalo` int(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci")->execute();

        $this->db->createCommand("CREATE TABLE `kategoriak` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tipus` varchar(20) DEFAULT NULL,
            `fokategoria` varchar(100) NOT NULL DEFAULT '',
            `nev` varchar(100) NOT NULL DEFAULT '',
            `torolt` tinyint(1) NOT NULL DEFAULT 0,
            `rogzitve` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `felhasznalo` int(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci")->execute();

        $this->db->createCommand("CREATE TABLE `mozgas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `datum` date NOT NULL,
            `penztarca_id` int(11) NOT NULL,
            `tipus` int(11) NOT NULL DEFAULT 0,
            `kategoria_id` int(11) DEFAULT NULL,
            `osszeg` decimal(15,2) DEFAULT NULL,
            `torolt` tinyint(1) NOT NULL DEFAULT 0,
            `rogzitve` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `felhasznalo` int(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `kategoria_id` (`kategoria_id`),
            CONSTRAINT `mozgas_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoriak` (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci")->execute();

        $this->db->createCommand("CREATE TABLE `terv` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kategoria_id` int(11) DEFAULT NULL,
            `osszeg` decimal(15,2) DEFAULT NULL,
            `idoszak_tipus` enum('év','hónap') DEFAULT NULL,
            `idoszak` varchar(20) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `kategoria_id` (`kategoria_id`),
            CONSTRAINT `terv_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoriak` (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci")->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230502_190015_init cannot be reverted.\n";

        return false;
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
