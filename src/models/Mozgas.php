<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
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
            [['penztarca_id', 'felhasznalo'], 'required'],
            [['penztarca_id', 'tipus', 'kategoria_id', 'osszeg', 'felhasznalo'], 'safe'],
            [['datum'], 'default', 'value' => date('Y-m-d')],
            // rememberMe must be a boolean value
            ['osszeg', 'integer'],
        ];
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "mozgas";
    }

    public function attributeLabels() {
        return array(
            'datum' => 'Dátum',
            'penztarca_id' => 'Pénztárca',
            'tipus' => 'Típus',
            'kategoria_id' => 'Kategória',
            'osszeg' => 'Összeg',
        );
    }

}