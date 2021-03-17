<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Classe\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    private $em;
    private $panierService;

    public function __construct(PanierService $panierService, EntityManagerInterface $em) {

        $this->em = $em;
        $this->panierService = $panierService;


    }
    
    /**
     * @Route("/panier", name="panier_index")
     */
    public function index()
    {

        

        $panier = $this->panierService->getFull();;

        return $this->render('panier/index.html.twig', [
            'panier' => $panier
        ]);
    }




    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function add($id)
    {

        $this->panierService->add($id);


        return $this->redirectToRoute('panier_index');
    }

    /**
     * @Route("/panier/decrement/{id}", name="panier_decrementer")
     */
    public function decrementer($id)
    {

        $this->panierService->decrementer($id);


        return $this->redirectToRoute('panier_index');
    }


    /**
     * @Route("/panier/delete/{id}", name="panier_delete")
     */
    public function delete($id)
    {

        $this->panierService->delete($id);


        return $this->redirectToRoute('panier_index');
    }
}
