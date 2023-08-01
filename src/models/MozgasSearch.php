<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mozgas;
use Yii;

/**
 * MozgasSearch represents the model behind the search form of `app\models\Mozgas`.
 */
class MozgasSearch extends Mozgas
{
    use Cor4Search;

    public function getIndexes()
    {
        return [
            0 => 'datum',
            1 => 'kategoriak.nev',
            2 => 'osszeg',
        ];
    }

    public function getColumns()
    {
        return [
            0 => 'datum',
            1 => 'kategoria.nev',
            2 => 'osszeg',
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
        return self::find()->joinWith(['kategoria']);
    }
}
