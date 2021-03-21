<?php

namespace App\Controller;

use App\Classe\MailJet;
use App\Repository\HeadersRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProduitsRepository $pr, HeadersRepository $hr): Response
    {

        
        $produitsBest = $pr->findByIsBest(1);
        
        //$mj = new MailJet();
        //$mj->send('nicolas.olagnon@gmail.com', 'Nicolas Olagnon', 'Mon premier email', 'Bonjour Nico jespere ca farte ?');

        $headers = $hr->findAll();

        return $this->render('home/index.html.twig', [
            'produitsBest' => $produitsBest,
            'headers' => $headers
        ]);
    }
}
