<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path : '/', name: 'home')] 
    function index(Request $request) : Response {
        //dump(); // debug
        //dd($request); // debug + mort
        return $this->render(view: 'home_controlleur/index.html.twig');
    }
}
