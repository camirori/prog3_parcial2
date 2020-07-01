<?php

//consola > composer require firebase/php-jwt 

namespace App\Utils;

//require_once('vendor/autoload.php');    
use \Firebase\JWT\JWT;
use Exception;

class AuthJWT{
    private static $key= '1234';
    
    public static function crearJWT($payload){
        return JWT::encode($payload, AuthJWT::$key);
    }

    public static function generarPayload($user_data =''){
        $payload = array(
            "iss" => "localhost",
            "sub" => "",
            "aud" => "users",
            "iat" => time(),
            "nbf" => time() + 1,
            "exp" => time() + 3000, //1 min = +60
            "data" => $user_data
        );
        return $payload;
    }

    public static function autentificar($jwt){
        try {
            return JWT::decode($jwt, AuthJWT::$key, array('HS256'));
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}