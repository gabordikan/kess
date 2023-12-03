<?php

namespace app\models;

use gabordikan\cor4\datatables\traits\SearchModel;
use app\models\Mozgas;
use Yii;

/**
 * MozgasSearch represents the model behind the search form of `app\models\Mozgas`.
 */
class MozgasSearch extends Mozgas
{
    use SearchModel;

    public function getIndexes()
    {
        return [
            0 => 'penztarca.nev',
            1 => 'datum',
            2 => 'kategoriak.nev',
            3 => 'osszeg',
            4 => 'megjegyzes',
        ];
    }

    public function getColumns()
    {
        return [
            0 => 'penztarca.nev',
            1 => 'datum',
            2 => 'kategoriak.nev',
            3 => 'osszeg',
            4 => 'megjegyzes',
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
        return self::find()->joinWith(['penztarca', 'kategoriak'])->orderBy(['datum'=>'DESC']);
    }
}
