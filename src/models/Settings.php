<?php

namespace app\models;

use app\components\VerificationHelper;
use Yii;
use yii\base\Model;

class Settings extends Model
{
    public $email;
    public $phone;
    public $oldpassword;
    public $newpassword;
    public $newpassword2;

   /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'phone', 'oldpassword', 'newpassword', 'newpassword2'], 'safe'],
            [['oldpassword'], 'validateOldPassword'],
            [['newpassword', 'newpassword2'], 'validateNewPassword'],
            [['email'], 'validateEmail'],
            [['phone'], 'validatePhone'],
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findOne(["id" => Yii::$app->user->id]);
            if (!$user->validatePassword($this->oldpassword)) {
                $this->addError($attribute, 'A jelszó nem megfelelő');
            }
            if ($this->newpassword == "") {
                $this->validateNewPassword("newpassword", $params);
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
            if ($this->newpassword != $this->newpassword2) {
                $this->addError($attribute, 'A megadott két jelszó nem egyezik');
            }
            if(!VerificationHelper::verifyPasswordComplexity($this->newpassword)) {
               $this->addError($attribute, 'A jelszónak legalább 8 hosszúnak kell lennie és tartalmaznia kell legalább egy kisbetűt, egy nagybetűt, egy számot és egy speciális karaktert (!_*$%.,-)');
           }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(!VerificationHelper::verifyEmail($this->email)) {
                $this->addError($attribute, 'Hibás email cím formátum');
            }
        }
    }

    public function validatePhone($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(!VerificationHelper::verifyPhone($this->phone)) {
                $this->addError($attribute, 'Hibás telefonszám formátum');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'phone' => 'Telefon',
            'oldpassword' => 'Jelenlegi jelszó',
            'newpassword' => 'Új jelszó',
            'newpassword2' => 'Új jelszó újra',
        ];
    }
}
