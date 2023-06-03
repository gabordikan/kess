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
class Penztarca extends ActiveRecord
{

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'nev', 'deviza'], 'safe'],
            [['nev'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "penztarca";
    }

    public function attributeLabels()
    {
        return [
            'nev' => 'Név',
        ];
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public static function getEgyenleg($id)
    {
        return Yii::$app->db->createCommand("
            select ifnull(sum(tipus*osszeg),0) from mozgas 
            where felhasznalo = :felhasznalo and penztarca_id = :penztarca_id
                and torolt=0"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':penztarca_id' => $id])
        ->queryScalar();
    }

    public static function getOsszEgyenleg()
    {
        return Yii::$app->db->createCommand("
            select ifnull(sum(tipus*osszeg),0) from mozgas
            left join penztarca on penztarca.id = mozgas.penztarca_id
            where mozgas.felhasznalo = :felhasznalo
                and penztarca.torolt = 0
                and mozgas.torolt = 0"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id])
        ->queryScalar();
    }

    public static function getPenztarcak()
    {
        $penztarcak = self::findAll(['felhasznalo' => Yii::$app->user->id, 'torolt' => 0]);
        foreach ($penztarcak as $id => $penztarca) {
            $pt_arr[$penztarca->id] = $penztarca->nev. " (".number_format(self::getEgyenleg($penztarca->id), 0, ',', ' ').")";
        }

        return $pt_arr;
    }

    public static function getDevizaList()
    {
        return Penztarca::find()
            ->select('deviza')
            ->where(['felhasznalo' => Yii::$app->user->id, 'torolt' => 0])
            ->groupBy('deviza');
    }

}