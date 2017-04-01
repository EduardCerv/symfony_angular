<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

/**
 * Description of VideoController
 *
 * @author systemback
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;

class VideoController extends Controller {

    public function newAction(Request $request) {
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

            $json = $request->get('json', null);

            $data = Array(
                'status' => "error",
                'code' => 400,
                'msg' => "No data!"
            );
            if ($json != null) {
                $params = json_decode($json);

                $createdAt = new \Datetime('now');
                $updatedAt = new \Datetime('now');
                $user_id = ($identity->sub != null) ? $identity->sub : null;
                $title = ($params->title != null) ? $params->title : null;
                $description = ($params->description != null) ? $params->description : null;
                $status = ($params->status != null) ? $params->status : null;

                if ($user_id != null && $title != null) {
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository("BackendBundle:User")->findOneBy(
                            array(
                                "id" => $identity->sub
                            )
                    );
                    $video = new Video();
                    $video->setUser($user);
                    $video->setTitle($title);
                    $video->setDescription($description);
                    $video->setStatus($status);
                    $video->setCreatedAt($createdAt);
                    $video->setUpdatedAt($updatedAt);
                    $em->persist($video);
                    $em->flush();

                    $new_video = $em->getRepository('BackendBundle:Video')->findOneBy(
                            array(
                                "user" => $user,
                                "title" => $title,
                                "status" => $status,
                                "reatedAt" => $createdAt
                    ));

                    $data = Array(
                        'status' => "success",
                        'data' => $new_video,
                        'msg' => "Video saved!"
                    );
                }
            }
        }

        return $helpers->jsonResponse($data);
    }

    public function editAction(Request $request, $video_id) {
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

            $json = $request->get('json', null);

            $data = Array(
                'status' => "error",
                'code' => 400,
                'msg' => "No data!"
            );
            if ($json != null) {
                $params = json_decode($json);

                $updatedAt = new \Datetime('now');
                $user_id = ($identity->sub != null) ? $identity->sub : null;
                $title = ($params->title != null) ? $params->title : null;
                $description = ($params->description != null) ? $params->description : null;
                $status = ($params->status != null) ? $params->status : null;

                if ($user_id != null && $title != null) {
                    $em = $this->getDoctrine()->getManager();

                    $video = $em->getRepository('BackendBundle:Video')->findOneBy(Array(
                        "id" => $video_id
                    ));
                    $data = Array(
                        'status' => "error",
                        'code' => 400,
                        'msg' => "You can not edit this video!"
                    );
                    if (isset($identity->sub) && $video->getUser()->getId() == $identity->sub) {
                        $video->setTitle($title);
                        $video->setDescription($description);
                        $video->setStatus($status);
                        $video->setUpdatedAt($updatedAt);
                        $em->persist($video);
                        $em->flush();

                        $data = Array(
                            'status' => "success",
                            'msg' => "Video updated!"
                        );
                    }
                }
            }
        }
        return $helpers->jsonResponse($data);
    }

}
