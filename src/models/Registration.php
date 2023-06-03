<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Registration extends Model
{
    public $username;
    public $email;
    public $phone;
    public $password;
    public $passwordverification;
    public $kepcsa;

   /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'phone', 'password', 'passwordverification', 'kepcsa'], 'safe'],
            [['username', 'email', 'password', 'passwordverification', 'kepcsa'], 'required'],
            [['username'], 'validateUsername'],
            [['password', 'passwordverification'], 'validateNewPassword'],
            [['email'], 'validateEmail'],
            [['phone'], 'validatePhone'],
            [['kepcsa'], 'kepcsaValidation'],
        ];
    }

    public function kepcsaValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (
                strtolower($this->kepcsa) != "pi"
                & strtolower($this->kepcsa) != "\"pi\""
                & substr($this->kepcsa,0,10) != "1415926535"
                ) {
                $this->addError($attribute, 'Az ellenőrzés nem sikerült.');
                }
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne(["username" => $this->username]);
            if ($user) {
                $this->addError($attribute, 'Ez a felhasználónév már regisztrálva van. A jelszava: *************************');
            }
            if (!$user) {
                if (!preg_match("/^[a-zA-Z0-9]{5,}$/", $this->username)) {
                    $this->addError($attribute, 'A felhasználónév csak angol kis és nagybetűket, valamint számot tartalmazhat és legalább öt karakter hosszúnak kell lennie');
                }
            }
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateNewPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (strlen($this->password) < 8) {
                $this->addError($attribute, 'A jelszónak legalább 8 hosszúnak kell lennie');
            }
            if ($this->password != $this->passwordverification) {
                $this->addError($attribute, 'Az ellenőrző jelszó nem egyezik az elsővel');
            }
            if(!preg_match('/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!_*$%.,-]).*$/', $this->password)) {
                $this->addError($attribute, 'A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt, egy számot és egy speciális karaktert (!_*$%.,-) ');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(!preg_match('/^[\w+-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $this->email)) {
                $this->addError($attribute, 'Hibás email cím formátum');
            }
        }
    }

    public function validatePhone($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(!preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $this->phone)) {
                $this->addError($attribute, 'Hibás telefonszám formátum');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'phone' => 'Telefon',
            'password' => 'Jelszó',
            'passwordverification' => 'Jelszó újra',
            'kepcsa' => 'Add meg a pi első 10 tizedes számjegyét, vagy csak írd be, hogy "pi"',
        ];
    }
}
