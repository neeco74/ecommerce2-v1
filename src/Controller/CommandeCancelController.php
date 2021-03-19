<?php

namespace App\Controller;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeCancelController extends AbstractController
{
    
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }   
    
    
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="commande_cancel")
     */
    public function index($stripeSessionId): Response
    {

        $commande = $this->em->getRepository(Commandes::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$commande OR $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('commande/cancel.html.twig', [
            'commande' => $commande
        ]);
    }
}
