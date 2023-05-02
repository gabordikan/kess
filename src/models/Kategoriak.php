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
class Kategoriak extends ActiveRecord
{

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['id, tipus, fokategoria, nev'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "kategoriak";
    }

    public static function getKategoriak() {
        $kategoriak = Self::findAll(["felhasznalo" => 1]);

        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[$arr->tipus][$arr->fokategoria][$arr->id] = $arr->nev;
        }

//        echo "<PRE>";
//        var_dump($kat_arr); die();
        return $kat_arr;
    }

}