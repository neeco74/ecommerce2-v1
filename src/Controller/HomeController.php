<?php

namespace App\Controller;

use App\Classe\MailJet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        $mj = new MailJet();
        $mj->send('nicolas.olagnon@gmail.com', 'Nicolas Olagnon', 'Mon premier email', 'Bonjour Nico jespere ca farte ?');



        return $this->render('home/index.html.twig');
    }
}
