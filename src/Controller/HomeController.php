<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home_index", methods={"GET"})
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

}