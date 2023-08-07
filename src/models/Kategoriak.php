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

    public $fokategoria_;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['tipus', 'fokategoria', 'fokategoria_', 'technikai', 'nev'], 'safe'],
            [['tipus', 'nev'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "kategoriak";
    }

    public function attributeLabels()
    {
        return [
            "tipus" => "Típus",
            "fokategoria" => "Főkategória",
            "fokategoria_" => "Új főkategória",
            "nev" => "Név",
        ];
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }

        if ($this->fokategoria_ != "") {
            $this->fokategoria = $this->fokategoria_;
        }

        return parent::beforeValidate();
    }

    public static function getFokategoriakLista($indexed = false, $tipus = null) {
        if($tipus != null) {
            $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "torolt" => 0, "tipus" => $tipus])
            ->groupBy('fokategoria')
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC])->all();
        } else {
            $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "torolt" => 0])
            ->groupBy('fokategoria')
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC])->all();
        }
    $kat_arr = [];

    foreach ($kategoriak as $id=>$arr) {
        if ($indexed) {
            $kat_arr[] = $arr->fokategoria;
        } else {
            $kat_arr[$arr->fokategoria] = $arr->fokategoria;
        }
    }

    return $kat_arr;
    }

    public static function getKategoriak($tipus = null) {
        $kategoriak = Self::find()
        ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0])
        ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
    if ($tipus) {
        } else {
            $kategoriak = Self::find()
                ->where(["felhasznalo" => Yii::$app->user->id, "torolt" => 0])
                ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        }

        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[$arr->fokategoria][$arr->id] = $arr->nev;
        }

        return $kat_arr;
    }

    public static function getKategoriakLista($tipus = 'Kiadás') {
        $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0, "technikai" => 0])
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();

        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[] = $arr->fokategoria." - ".$arr->nev;
        }

        return $kat_arr;
    }

    public static function getFokategoriaSumTeny($fokategorianev, $tol, $ig, $tipus, $deviza = 'HUF') {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from mozgas 
            left join penztarca on penztarca.id = mozgas.penztarca_id
            where kategoria_id in (select id from kategoriak where fokategoria = :fokategorianev and felhasznalo = :felhasznalo and technikai = 0)
                and mozgas.felhasznalo = :felhasznalo
                and mozgas.datum >= :tol
                and mozgas.datum <= :ig
                and mozgas.torolt = 0
                and mozgas.tipus= :tipus
                and penztarca.torolt = 0
                and penztarca.deviza = :deviza"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':fokategorianev' => $fokategorianev, ':tol' => $tol, ':ig' => $ig, ':tipus' => $tipus, ':deviza' => $deviza])
        ->queryScalar();
    }

    public static function getFokategoriakListaEgyenleg($tol, $ig, $tipus, $deviza = 'HUF') {
        $fokategoriak = self::getFokategoriakLista();

        $fokat_arr = [];

        foreach ($fokategoriak as $fokategorianev) {
            $fokat_arr[] = self::getFokategoriaSumTeny($fokategorianev, $tol, $ig, $tipus, $deviza);
        }

        return $fokat_arr;
    }

    public static function getFokategoriaSzin($fokategorianev) {
        $hash = md5($fokategorianev);
        return "#".substr($hash,0,6);
    } 

    public static function getFokategoriakSzinek() {
        $fokategoriak = self::getFokategoriakLista();

        $fokat_arr = [];

        foreach ($fokategoriak as $fokategorianev) {
            $fokat_arr[] = self::getFokategoriaSzin($fokategorianev);
        }

        return $fokat_arr;
    }

    public static function getKategoriaSumTerv($kategoria_id, $tol, $ig, $deviza = 'HUF') {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from terv 
            where kategoria_id = :kategoria_id
                and felhasznalo = :felhasznalo
                and idoszak >= :tol
                and idoszak <= :ig
                and torolt=0
                and deviza = :deviza"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':kategoria_id' => $kategoria_id, ':tol' => substr($tol,0,7), ':ig' => substr($ig,0,7), ':deviza' => $deviza])
        ->queryScalar();
    }

    public static function getKategoriaSumTeny($kategoria_id, $tol, $ig, $deviza = 'HUF') {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from mozgas
            left join penztarca on penztarca.id=mozgas.penztarca_id
            where kategoria_id = :kategoria_id
                and mozgas.felhasznalo = :felhasznalo
                and mozgas.datum >= :tol
                and mozgas.datum <= :ig
                and mozgas.torolt = 0
                and penztarca.torolt = 0
                and penztarca.deviza = :deviza"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':kategoria_id' => $kategoria_id, ':tol' => $tol, ':ig' => $ig, ':deviza' => $deviza])
        ->queryScalar();
    }

    public static function getSumTerv($tipus = 'Kiadás', $tol, $ig, $deviza = 'HUF') {
        $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0, "technikai"=> 0])
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        
        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[] = self::getKategoriaSumTerv($arr->id, $tol, $ig, $deviza);
        }

        return $kat_arr;
    }

    public static function getSumTeny($tipus = 'Kiadás', $tol, $ig, $deviza = 'HUF') {
        $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0, "technikai"=> 0])
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        
        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[] = self::getKategoriaSumTeny($arr->id, $tol, $ig, $deviza);
        }

        return $kat_arr;
    }

    public static function getMostUsedKategoriak($tipus = -1, $penztarca_id = 0) {
        $kategoriak = Yii::$app->db->createCommand("
            SELECT kategoria_id as id, kategoriak.nev as nev, count(kategoria_id) c
                FROM mozgas
                LEFT JOIN kategoriak on kategoriak.id = mozgas.kategoria_id
                WHERE mozgas.felhasznalo = :felhasznalo
                    AND mozgas.tipus = :tipus
                    AND mozgas.torolt = 0
                    AND penztarca_id = :penztarca_id
                GROUP BY kategoria_id
                ORDER BY c DESC
                LIMIT 0, 3
                ")
                ->bindValues([':felhasznalo' => Yii::$app->user->id, ':tipus' => $tipus, ':penztarca_id' => $penztarca_id])
                ->queryAll();
        return $kategoriak;
    }

}