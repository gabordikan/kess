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
            [['tipus', 'fokategoria', 'nev'], 'required'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "kategoriak";
    }

    public function beforeValidate()
    {
        if (!Yii::$app->user->isGuest) {
            $this->felhasznalo = Yii::$app->user->id;
        }

        return parent::beforeValidate();
    }

    public static function getFokategoriakLista() {
        $kategoriak = Self::find()
        ->where(["felhasznalo" => Yii::$app->user->id, "torolt" => 0])
        ->groupBy('fokategoria')
        ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC])->all();

    $kat_arr = [];

    foreach ($kategoriak as $id=>$arr) {
        $kat_arr[$arr->fokategoria] = $arr->fokategoria;
    }

    return $kat_arr;
    }

    public static function getKategoriak($tipus = null) {
        $kategoriak = Self::find()
        ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus])
        ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
    if ($tipus) {
        } else {
            $kategoriak = Self::find()
                ->where(["felhasznalo" => Yii::$app->user->id])
                ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        }

        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[$arr->tipus][$arr->fokategoria][$arr->id] = $arr->nev;
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

    public static function getFokategoriaSumTeny($fokategorianev, $tol, $ig, $tipus) {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from mozgas 
            where kategoria_id in (select id from kategoriak where fokategoria = :fokategorianev and felhasznalo = :felhasznalo and technikai = 0)
                and felhasznalo = :felhasznalo
                and datum >= :tol
                and datum <= :ig
                and torolt=0
                and tipus= :tipus"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':fokategorianev' => $fokategorianev, ':tol' => $tol, ':ig' => $ig, ':tipus' => $tipus])
        ->queryScalar();
    }

    public static function getFokategoriakListaEgyenleg($tol, $ig, $tipus) {
        $fokategoriak = self::getFokategoriakLista();

        $fokat_arr = [];

        foreach ($fokategoriak as $fokategorianev) {
            $fokat_arr[] = self::getFokategoriaSumTeny($fokategorianev, $tol, $ig, $tipus);
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

    public static function getKategoriaSumTerv($kategoria_id, $tol, $ig) {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from terv 
            where kategoria_id = :kategoria_id
                and felhasznalo = :felhasznalo
                and idoszak >= :tol
                and idoszak <= :ig
                and torolt=0"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':kategoria_id' => $kategoria_id, ':tol' => substr($tol,0,7), ':ig' => substr($ig,0,7)])
        ->queryScalar();
    }

    public static function getKategoriaSumTeny($kategoria_id, $tol, $ig) {
        return Yii::$app->db->createCommand("
            select ifnull(sum(osszeg),0) from mozgas 
            where kategoria_id = :kategoria_id
                and felhasznalo = :felhasznalo
                and datum >= :tol
                and datum <= :ig
                and torolt=0"
        )
        ->bindValues([':felhasznalo' => Yii::$app->user->id, ':kategoria_id' => $kategoria_id, ':tol' => $tol, ':ig' => $ig])
        ->queryScalar();
    }

    public static function getSumTerv($tipus = 'Kiadás', $tol, $ig) {
        $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0, "technikai"=> 0])
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        
        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[] = self::getKategoriaSumTerv($arr->id, $tol, $ig);
        }

        return $kat_arr;
    }

    public static function getSumTeny($tipus = 'Kiadás', $tol, $ig) {
        $kategoriak = Self::find()
            ->where(["felhasznalo" => Yii::$app->user->id, "tipus" => $tipus, "torolt" => 0, "technikai"=> 0])
            ->orderBy(['tipus'=>SORT_ASC, 'fokategoria'=>SORT_ASC, 'nev'=>SORT_ASC])->all();
        
        $kat_arr = [];

        foreach ($kategoriak as $id=>$arr) {
            $kat_arr[] = self::getKategoriaSumTeny($arr->id, $tol, $ig);
        }

        return $kat_arr;
    }

}