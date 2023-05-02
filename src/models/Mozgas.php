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

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['penztarca'], 'required'],
            [['penztarca', 'tipus', 'kategoria_id', 'osszeg'], 'safe'],
            // rememberMe must be a boolean value
            ['osszeg', 'integer'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "mozgas";
    }

}