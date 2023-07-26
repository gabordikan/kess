<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Terv extends ActiveRecord
{

    public function init() {
        parent::init();
        $this->deviza = 'HUF';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['felhasznalo'], 'required'],
            [['kategoria_id', 'deviza', 'osszeg', 'idoszak_tipus', 'idoszak', 'felhasznalo'], 'safe'],
            [['idoszak'], 'default', 'value' => date('Y-m')],
            // rememberMe must be a boolean value
            ['osszeg', 'number'],
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

    public function getKategoria() {
        return $this->hasOne(Kategoriak::class, ['id' => 'kategoria_id']);
    }

    public static function getTervSum($tipus, $tol, $ig, $deviza = 'HUF') {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from terv 
            where kategoria_id in (select id from kategoriak where tipus = :tipus and felhasznalo = :felhasznalo and technikai = 0)
                and felhasznalo = :felhasznalo
                and idoszak >= :tol
                and idoszak <= :ig
                and torolt=0
                and deviza = :deviza"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':tipus' => $tipus, ':tol' => $tol, ':ig' => $ig, ':tipus' => $tipus, ':deviza' => $deviza])
        ->queryScalar();
    }

    public static function getTenySum($tipus, $tol, $ig, $deviza = 'HUF') {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from mozgas 
            left join penztarca on penztarca.id = mozgas.penztarca_id
            where kategoria_id in (select id from kategoriak where tipus = :tipus and felhasznalo = :felhasznalo and technikai = 0)
                and mozgas.felhasznalo = :felhasznalo
                and datum >= :tol
                and datum <= :ig
                and mozgas.torolt=0
                and penztarca.deviza = :deviza"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':tipus' => $tipus, ':tol' => $tol, ':ig' => $ig, ':tipus' => $tipus, ':deviza' => $deviza])
        ->queryScalar();
    }

    public static function copyPlan($idoszak)
    {
        $elozoidoszak = date('Y-m', strtotime($idoszak . ' -1 month'));

        Yii::$app->db->createCommand(
            "
                insert into terv (
                    kategoria_id,
                    osszeg,
                    idoszak_tipus,
                    idoszak,
                    felhasznalo
                    )
                (select                     
                    kategoria_id,
                    osszeg,
                    idoszak_tipus,
                    :idoszak,
                    felhasznalo
                from terv
                where
                    idoszak=:elozoidoszak
                    and felhasznalo = :felhasznalo
                    and torolt = 0
                    and osszeg != 0)
            "
        )
        ->bindValues([
            ':idoszak' => $idoszak,
            ':elozoidoszak' => $elozoidoszak,
            ':felhasznalo' => Yii::$app->user->id
        ])
        ->execute();
    }
}