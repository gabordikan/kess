<?php

use app\models\User;
use yii\db\Migration;

/**
 * Class m230602_103200_user_table
 */
class m230602_103200_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("CREATE TABLE `user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(40) NOT NULL DEFAULT '',
            `password` varchar(100) NOT NULL DEFAULT '',
            `authKey` varchar(100) NOT NULL DEFAULT '',
            `accessToken` varchar(100) NOT NULL DEFAULT '',
            `email` varchar(100) NOT NULL DEFAULT '',
            `phone` varchar(100) NOT NULL DEFAULT '',
            `torolt` tinyint(1) not null default 0,
            `rogzitve` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci")->execute();

          $this->db->createCommand("
            INSERT INTO user set 
                id=1,
                username='admin',
                password='" .password_hash('Rek0Gaq3Lud3Gut6',PASSWORD_DEFAULT,[])."',
                authKey='kesskeyadmin',
                accessToken='".md5(User::randomString())."',
                email='dix@dix.hu',
                phone='+36305522193'
          ")->execute();

          $this->db->createCommand("
          INSERT INTO user set 
              id=100,
              username='dikan',
              password='" .password_hash('Tr4ub1s0d$',PASSWORD_DEFAULT,[])."',
              authKey='kesskeydikan',
              accessToken='".md5(User::randomString())."',
              email='gabor@dikan.hu',
              phone='+36305522193'
        ")->execute();

        $this->db->createCommand("
        INSERT INTO user set 
            id=101,
            username='juli',
            password='".password_hash('C43s4r78_',PASSWORD_DEFAULT,[])."',
            authKey='kesskeyjuli',
            accessToken='".md5(User::randomString())."',
            email='julimedest78@gmail.com',
            phone='+36303359686'
      ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand("
        DROP TABLE user;
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
