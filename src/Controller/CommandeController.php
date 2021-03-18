<?php

namespace App\Controller;

use App\Classe\PanierService;
use App\Form\CommandeFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande_index")
     */
    public function index(PanierService $panierService): Response
    {

        if(!$this->getUser()->getAddresses()->getValues()) {

            return $this->redirectToRoute('account_address_add');
        }


        $form = $this->createForm(CommandeFormType::class, null, [
            'user' => $this->getUser(),
        ]);


        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'panier' => $panierService->getFull()
        ]);
    }
}
