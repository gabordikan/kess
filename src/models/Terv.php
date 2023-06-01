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
}