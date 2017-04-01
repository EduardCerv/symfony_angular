<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppController extends Controller {

    public function indexAction() {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    public function loginAction(Request $request) {
        $helpers = $this->get('app.helpers');
        $jwt_auth = $this->get('app.jwt_auth');
        $json = $request->headers->get("json", null);

        if ($json != null) {
            $params = json_decode($json);
            $email_user = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $hash = (isset($params->hash)) ? $params->hash : null;
            $pwd = hash('sha256', $password);

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid";
            $email_valid = $this->get('validator')->validate($email_user, $emailConstraint);

            if (COUNT($email_valid) == 0 && $password != null) {

                if ($hash == null) {
                    $singup = $jwt_auth->singup($email_user, $pwd);
                } else {
                    $singup = $jwt_auth->singup($email_user, $pwd, TRUE);
                }

                return new JsonResponse($singup);
            } else {
                return $helpers->jsonResponse(
                                array(
                                    'status' => TRUE,
                                    'data' => "Error login not valid!!!"
                                )
                );
            }
        } else {
            return $helpers->jsonResponse(
                            array(
                                'status' => TRUE,
                                'data' => "Error post method incorrect"
                            )
            );
        }
    }

    public function pruebaAction(Request $request) {
        $helpers = $this->get('app.helpers');

        $hash = $request->get("authorization", null);

        $check = $helpers->authCheck($hash);
        var_dump($check);
        die();

        /*
          $em = $this->getDoctrine()->getManager();
          $usuarios = $em->getRepository('BackendBundle:User')->findAll();
         */
        return $helpers->jsonResponse($usuarios);
    }

}
