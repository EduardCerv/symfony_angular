<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth {

    public $manager, $key;

    public function __construct($manager) {
        $this->manager = $manager;
        $this->key = "clave-secreta";
    }

    public function singup($email, $password, $getHash = NULL) {
        $key = $this->key;

        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy(
                Array(
                    "email" => $email,
                    "password" => $password
        ));
        if ($user == null) {
            return Array(
                'status' => "error",
                'msg' => "User not found!!"
            );
        }
        $token = array(
            "sub" => $user->getId(),
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "surname" => $user->getSurname(),
            "password" => $user->getPassword(),
            "image" => $user->getImage(),
            "iat" => time(),
            "exp" => time() + (7 * 24 * 60 * 60)
        );

        $jwt = JWT::encode($token, $key, "HS256");
        $decode = JWT::decode($jwt, $key, array("HS256"));

        if ($getHash != null) {
            return $jwt;
        } else {
            return $decode;
        }
    }

    public function checkToken($jwt, $getIdenty = false) {
        $key = $this->key;
        $auth = false;
        try {
            $decode = JWT::decode($jwt, $key, array("HS256"));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (isset($decode->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
        if ($getIdenty == true) {
            return $decode;
        } else {
            return $auth;
        }
    }

}
