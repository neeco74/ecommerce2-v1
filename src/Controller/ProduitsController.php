<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Produits;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
   
   
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    
    /**
     * @Route("/produits", name="produits")
     */
    public function index(Request $request)
    {
        $search = new Search();
        $form = $this->createForm(SearchFormType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $produits = $this->em->getRepository(Produits::class)->findWithSearch($search);
            
        }
        else {
            $produits = $this->em->getRepository(Produits::class)->findAll();
        }

        return $this->render('produits/index.html.twig', [
            'produits' => $produits,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/produits/{slug}", name="produits_show")
     */
    public function show($slug)
    {

        $produit = $this->em->getRepository(Produits::class)->findOneBySlug($slug);

        if(!$produit) {

            return $this->redirectToRoute('produits');
        }

        return $this->render('produits/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
