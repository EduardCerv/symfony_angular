<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;

/**
 * Description of UserController
 *
 * @author systemback
 */
class UserController extends Controller {

    public function newAction(Request $request) {
        $helpers = $this->get('app.helpers');

        $json = $request->get('json', null);

        if ($json != null) {
            $createAt = new \Datetime("now");
            $image = null;
            $role = "user";
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $password = (isset($params->password)) ? $params->password : null;
            //cifrar la contrasena
            $pwd = hash('sha256', $password);

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid";
            $email_valid = $this->get('validator')->validate($email, $emailConstraint);

            if ($email != null && count($email_valid) == 0 && $password != null && $name != null && $surname != null) {
                $user = new User();
                $user->setCreatedAt($createAt);
                $user->setImage($image);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surname);
                $user->setPassword($pwd);

                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository("BackendBundle:User")->findBy(
                        array(
                            "email" => $email
                        )
                );
                if (count($isset_user) == 0) {
                    $em->persist($user);
                    $em->flush();
                    $data = Array(
                        'status' => "success",
                        'msg' => "New user created!"
                    );
                } else {
                    $data = Array(
                        'status' => "error",
                        'code' => 400,
                        'msg' => "User not created, duplicated!"
                    );
                }
            }
        } else {
            $data = Array(
                'status' => "error",
                'code' => 400,
                'msg' => "User not created"
            );
        }

        return $helpers->jsonResponse($data);
    }

    public function editAction(Request $request) {
        $helpers = $this->get('app.helpers');

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        $data = Array(
            'status' => "error",
            'code' => 400,
            'msg' => "User not authorized!"
        );
        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(
                    array(
                        "id" => $identity->sub
                    )
            );

            $json = $request->get('json', null);
            $params = json_decode($json);

            if ($json != null) {
                $email = (isset($params->email)) ? $params->email : null;
                $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
                $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
                $password = (isset($params->password)) ? $params->password : null;


                $emailConstraint = new Assert\Email();
                $emailConstraint->message = "This email is not valid";
                $email_valid = $this->get('validator')->validate($email, $emailConstraint);

                if ($email != null && count($email_valid) == 0 && $name != null && $surname != null) {
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);
                    if ($password = !null) {
                        //cifrar la contrasena
                        $pwd = hash('sha256', $password);
                        $user->setPassword($pwd);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $isset_user = $em->getRepository("BackendBundle:User")->findBy(
                            array(
                                "email" => $email
                            )
                    );
                    if (count($isset_user) == 0 || $identity->email == $email) {
                        $em->persist($user);
                        $em->flush();
                        $data = Array(
                            'status' => "success",
                            'msg' => "User updated!"
                        );
                    } else {
                        $data = Array(
                            'status' => "error",
                            'code' => 400,
                            'msg' => "User not updated!"
                        );
                    }
                }
            } else {

            }
        }

        return $helpers->jsonResponse($data);
    }

    public function uploadImageAction(Request $request) {
        $helpers = $this->get('app.helpers');

        $hash = $request->get("authorization", null);
        $authCheck = $helpers->authCheck($hash);

        $data = Array(
            'status' => "error",
            'code' => 400,
            'msg' => "User not authorized!"
        );
        if ($authCheck == true) {

            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BackendBundle:User")->findOneBy(
                    array(
                        "id" => $identity->sub
                    )
            );

            //upload image
            $file = $request->files->get('image');
            if (!empty($file) && $file != null) {
                $ext = $file->guessExtension();
                $file_name = time() . "." . $ext;
                $file->move("uploads/user", $file_name);
                $user->setImage($file_name);
                $em->persist($user);
                $em->flush();

                $data = Array(
                    'status' => "success",
                    'msg' => "User updated!"
                );
            } else {

                $data = Array(
                    'status' => "error",
                    'code' => 400,
                    'msg' => "Image not upload!"
                );
            }
        }

        return $helpers->jsonResponse($data);
    }

}
