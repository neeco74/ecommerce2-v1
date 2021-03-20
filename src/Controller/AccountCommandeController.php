<?php

namespace App\Controller;

use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountCommandeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    /**
     * @Route("/account/mes-commandes", name="account_commande")
     */
    public function index(CommandesRepository $cr): Response
    {

        $commandes = $cr->findSuccessCommandes($this->getUser());


        return $this->render('account/commande.html.twig', [
            'commandes' => $commandes
        ]);
    }


    /**
     * @Route("/account/mes-commandes/{reference}", name="account_commande_show")
     */
    public function show(CommandesRepository $cr, $reference): Response
    {

        $commande = $cr->findOneByReference($reference);

        if(!$commande OR $commande->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_commande');
        }

        return $this->render('account/commande_show.html.twig', [
            'commande' => $commande
        ]);
    }
}
