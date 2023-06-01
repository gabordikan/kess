<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Terv extends ActiveRecord
{

    public function init() {
        parent::init();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['felhasznalo'], 'required'],
            [['kategoria_id', 'osszeg', 'idoszak_tipus', 'idoszak', 'felhasznalo'], 'safe'],
            [['idoszak'], 'default', 'value' => date('Y-m')],
            // rememberMe must be a boolean value
            ['osszeg', 'integer'],
        ];
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }

        $this->idoszak_tipus = 'hónap'; //TODO select

        return parent::beforeValidate();
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "terv";
    }

    public function attributeLabels() {
        return array(
            'datum' => 'Dátum',
            'kategoria_id' => 'Kategória',
            'osszeg' => 'Összeg',
            'idoszak_tipus' => 'Időszak típusa',
            'idoszak' => 'Időszak',
        );
    }

    public static function getTervSum($tipus, $tol, $ig) {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from terv 
            where kategoria_id in (select id from kategoriak where tipus = :tipus and felhasznalo = :felhasznalo)
                and felhasznalo = :felhasznalo
                and idoszak >= :tol
                and idoszak <= :ig
                and torolt=0"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':tipus' => $tipus, ':tol' => $tol, ':ig' => $ig, ':tipus' => $tipus])
        ->queryScalar();
    }
}