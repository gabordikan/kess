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
                'Víz',
                'Gáz',
                'Villany',
                'Szemét',
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
                'Ajándék',
            ),
            'Megtakarítás' => array(
                'Megtakarítás',
            ),
            'Biztosítás' => array(
                'Életbiztosítás',
                'Utasbiztosítás',
                'Lakásbiztosítás',
            ),
            'Adók' => array(
                'SZJA',
                'Ingatlan',
                'Gépjármű',
            ),
            'Egyéb' => array(
                'Átvezetés',
                'Korrekció',
                'Egyéb',
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
                        "felhasznalo" => 1,
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
