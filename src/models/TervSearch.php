<?php

namespace app\models;

use gabordikan\cor4\datatables\traits\SearchModel;
use app\models\Mozgas;
use Yii;

/**
 * MozgasSearch represents the model behind the search form of `app\models\Mozgas`.
 */
class TervSearch extends Terv
{
    use SearchModel;

    public $_idoszak;
    public $_deviza;

    public function getIndexes()
    {
        return [
            0 => 'terv.idoszak',
            1 => 'terv.tipus',
            2 => 'kategoriak.fokategoria',
            3 => 'kategoriak.nev',
            4 => 'terv.osszeg',
        ];
    }

    public function addDefaultCondition($query) 
    {
        $query->andWhere(
            [
                'terv.felhasznalo' => Yii::$app->user->id,
                'terv.idoszak' => $this->_idoszak,
                'terv.deviza' => $this->_deviza,
                'terv.torolt' => 0,
            ]
        );
        return $query;
    }

    public static function getQuery() {
        return self::find()
            ->joinWith('kategoriak');
    }
}
