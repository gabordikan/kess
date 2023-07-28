<?php

    namespace app\components;

    class VerificationHelper {

        public static function verifyPasswordComplexity($password)
        {
            if(!(strlen($password) >= 8 && preg_match('/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!_*$%.,-]).*$/', $password)) // minimum 8 es kisbetu/nagybetu/szam/specialis
            && !(strlen($password) >=15 && preg_match('/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/', $password))) //minimum 15 es kisbetu/nagybetu/szam
            {
                return false; 
            }
            return true;
        }

        public static function verifyEmail($email)
        {
            if(!preg_match('/^[\w+-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $email)) {
                return false;
            }
            return true;
        }

        public static function verifyPhone($phone)
        {
            if(!preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $phone)) {
                return false;
            }
            return true;
        }
    }
?>