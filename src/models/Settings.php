<?php

namespace app\models;

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
            if (strlen($this->newpassword) < 8) {
                $this->addError($attribute, 'A jelszónak legalább 8 hosszúnak kell lennie');
            }
            if ($this->newpassword != $this->newpassword2) {
                $this->addError($attribute, 'A megadott két jelszó nem egyezik');
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