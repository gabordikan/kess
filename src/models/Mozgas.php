<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Mozgas extends ActiveRecord
{

    public function init() {
        if ($this->isNewRecord) {
            $this->datum = date('Y-m-d');
        }
        parent::init();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['penztarca_id', 'felhasznalo', 'osszeg', 'tipus', 'kategoria_id'], 'required'],
            [['penztarca_id', 'tipus', 'kategoria_id', 'osszeg', 'felhasznalo', 'megjegyzes'], 'safe'],
            [['datum'], 'default', 'value' => date('Y-m-d')],
            // rememberMe must be a boolean value
            ['osszeg', 'number'],
        ];
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }

        //var_dump(Kategoriak::findOne(['id' => $this->kategoria_id])->tipus); die();

        $this->tipus = Kategoriak::findOne(['id' => $this->kategoria_id])->tipus == 'Bevétel' ? 1 : -1;

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "mozgas";
    }

    public function attributeLabels() 
    {
        return array(
            'datum' => 'Dátum',
            'penztarca_id' => 'Pénztárca',
            'tipus' => 'Típus',
            'kategoria_id' => 'Kategória',
            'osszeg' => 'Összeg',
            'megjegyzes' => 'Megjegyzés',
        );
    }

    public function getPenztarca()
    {
        return $this->hasOne(Penztarca::class, ['id' => 'penztarca_id']);
    }

    public function getKategoria()
    {
        return $this->hasOne(Kategoriak::class, ['id' => 'kategoria_id']);
    }

    public function getFelhasznalo()
    {
        return $this->hasOne(User::class, ['id' => 'felhasznalo_id']);
    }
}