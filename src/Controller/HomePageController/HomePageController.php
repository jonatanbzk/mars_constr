<?php


namespace App\Controller\HomePageController;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{

    /**
     * @Route("/homepage", name="homepage")
     * @return Response
     */
    public function homepage()
    {
        return $this->render('homepage/homepage.html.twig');
    }

}