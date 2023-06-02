<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function randomString($l = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $l; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return "user";
    }

    public function beforeValidate()
    {

        if ($this->authKey == "") {
            $this->authKey = "kesskey".$this->username;
        }

        if ($this->accessToken == "") {
            $this->accessToken = self::randomString();
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(["id" => $id]);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(["accessToken" => $token]);
/*        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(["username" => $username]);
/*        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function savePassword($password)
    {
        $this->password = password_hash($password,PASSWORD_DEFAULT,[]);
        $this->save();
    }
}
