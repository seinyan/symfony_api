<?php

namespace App\Controller;

use App\Services\EmailService;
use App\Services\JWTService;
use donatj\UserAgent\UserAgentParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;
use Jenssegers\Agent\Agent;

class HomeController extends RestController
{
    /**
     * @Route("/test", methods={"GET", "POST"})
     */
    public function test(JWTService $JWTService)
    {

        $phone = '7-424-332-43-24';

        dump( preg_replace('/[^0-9]/', '', $phone));
        exit;

        $JWTService->test();

        return new JsonResponse([
            'masg' => 134
        ]);
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index()
    {
        return $this->redirectToRoute('app.swagger_ui');
        return $this->render('home/index.html.twig');
    }



}
