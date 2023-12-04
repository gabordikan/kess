<?php

namespace app\models;

use gabordikan\cor4\datatables\traits\SearchModel;
use app\models\Mozgas;
use Yii;

/**
 * MozgasSearch represents the model behind the search form of `app\models\Mozgas`.
 */
class NapiegyenlegSearch extends Mozgas
{
    public $nyito;
    public $bevetel;
    public $kiadas;
    public $egyenleg;
    public $deviza;
    public $penztarca_nev;

    use SearchModel;

    public function getIndexes()
    {
        return [
            0 => 'datum',
            1 => 'penztarca.nev',
        ];
    }

    public function addDefaultCondition($query) 
    {
        $query->andWhere(
            [
                'mozgas.felhasznalo' => Yii::$app->user->id,
                'mozgas.torolt' => 0,
            ]
        );
        return $query;
    }

    public static function getQuery() {
        return self::find()->select([
            'datum',
            'penztarca.nev as penztarca_nev',
            'penztarca.deviza as deviza',
            '(
                select ifnull(sum(tipus * osszeg),0)
                from mozgas m2 
                where m2.datum < mozgas.datum 
                    and m2.torolt = 0
                    and m2.penztarca_id = mozgas.penztarca_id
                    and m2.felhasznalo = mozgas.felhasznalo
            ) as nyito',
            '(
                select ifnull(sum(osszeg),0)
                from mozgas m2 
                where m2.datum = mozgas.datum 
                    and m2.torolt = 0
                    and m2.penztarca_id = mozgas.penztarca_id
                    and m2.felhasznalo = mozgas.felhasznalo
                    and tipus = 1
            ) as bevetel',
            '(
                select ifnull(sum(osszeg),0)
                from mozgas m2 
                where m2.datum = mozgas.datum 
                    and m2.torolt = 0
                    and m2.penztarca_id = mozgas.penztarca_id
                    and m2.felhasznalo = mozgas.felhasznalo
                    and tipus = -1
            ) as kiadas',
            '(
                    select ifnull(sum(tipus * osszeg),0)
                    from mozgas m2 
                    where m2.datum <= mozgas.datum 
                        and m2.torolt = 0
                        and m2.penztarca_id = mozgas.penztarca_id
                        and m2.felhasznalo = mozgas.felhasznalo
                ) as egyenleg',
            ])
            ->joinWith('penztarca')
            ->groupBy(['datum', 'penztarca_id']);
    }
}
