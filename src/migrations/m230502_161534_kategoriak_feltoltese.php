<?php

use yii\db\Migration;

/**
 * Class m230502_161534_kategoriak_feltoltese
 */
class m230502_161534_kategoriak_feltoltese extends Migration
{

    private $kategoriak = 
    array(
        'Bevétel' => array(
            'Árbevétel' => array(
                'Wombex',
                'Netlapok',
                'Juli',
                'Anyu',
                'Húgom',
                'Bofa',
                'Após',
            ),
            'Fizetés' => array(
                'Bér',
                'Egyéb',
            ),
            'Egyéb' => array(
                'Nyitó',
                'Átvezetés',
                'Kamat',
                'Korrekció',
            ),
        ),
        'Kiadás' => array(
            'Étel-Ital' => array(
                'Bevásárlás',
                'Készétel',
                'Étterem',
                'Kávézó',
            ),
            'Rezsi' => array(
                'Telekom',
                'Telekom törlesztő',
                'Netflix',
                'Disney+',
                'SkyShowTime',
                'Spotify',
                'YouTube Premium',
                'Google Drive',
                'Apple iCloud',
                'Microsoft OneDrive',
                'Telegram',
                'OF',
                'VPSCheap'
            ),
            'Autó' => array(
                'Törlesztő',
                'Üzemanyag',
                'KGFB',
                'Casco',
                'Szerviz',
                'Gumicsere',
                'Matrica',
                'Parkolás',
                'Egyéb',
            ),
            'Lakás' => array(
                'Törlesztő',
                'Egyéb',
            ),
            'Ajándék' => array(
                'F',
            ),
            'Megtakarítás' => array(
                'Startszámla',
                'Raiffeisen',
                'Uniqua',
                'Nyugdíj',
                'OTP1',
                'OTP2',
            ),
            'Biztosítás' => array(
                'Életbiztosítás',
                'Utasbiztosítás',
            ),
            'Adók' => array(
                'SZJA',
                'TB',
                'Szociális hozzájárulás',
                'TAO',
                'IPA',
                'Ingatlan',
                'Gépjármű',
            ),
            'Egyéb' => array(
                'Tifi ebéd',
                'Anyu telefonszámla',
                'Anyunak egyéb',
                'Átvezetés',
                'Korrekció',
            ),
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->kategoriak as $tipus=>$fokategoriak) {
            foreach ($fokategoriak as $fokategoria=>$kategoriak) {
                foreach ($kategoriak as $kategoria) {
                    echo $tipus."/".$fokategoria."/".$kategoria."\n";
                    $this->insert("kategoriak", array(
                        "tipus" => $tipus,
                        "fokategoria" => $fokategoria,
                        "nev" => $kategoria,
                        "felhasznalo" => 100,
                    ));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230502_161534_kategoriak_feltoltese cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230502_161534_kategoriak_feltoltese cannot be reverted.\n";

        return false;
    }
    */
}
