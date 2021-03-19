<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Classe\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeSuccessController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="commande_merci")
     */
    public function index(PanierService $panierService, $stripeSessionId): Response
    {

        $commande = $this->em->getRepository(Commandes::class)->findOneByStripeSessionId($stripeSessionId);



        if(!$commande OR $commande->getUser() != $this->getUser()) {

            return $this->redirectToRoute('home');
        }
        $panierService->remove();
        if(!$commande->getIsPaid()) {

            $panierService->remove();

            $commande->setIsPaid(1);
            $this->em->flush();






        }

        return $this->render('commande/success.html.twig', [
            'commande' => $commande
        ]);
    }
}
