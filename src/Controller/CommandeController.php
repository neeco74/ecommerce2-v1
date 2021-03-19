<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Classe\PanierService;
use App\Form\CommandeFormType;
use App\Entity\CommandeDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    /**
     * @Route("/commande", name="commande_index")
     */
    public function index(PanierService $panierService, Request $request): Response
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

    /**
     * @Route("/commande/recapitulatif", name="commande_recap", methods={"POST"})
     */
    public function add(PanierService $panierService, Request $request): Response
    {


        $form = $this->createForm(CommandeFormType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $date = new \DateTime();
            $transporteurs = $form->get('transporteur')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
            $delivery_content .= '<br/>'.$delivery->getTelephone();

            if($delivery->getSociete()) {
                $delivery_content .= '<br/>'.$delivery->getSociete();
            
            }
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostalCode().' '.$delivery->getVille();
            $delivery_content .= '<br/>'.$delivery->getPays();
            
            // Enregistrer ma commande
            $commande = new Commandes();
            $commande->setUser($this->getUser());
            $commande->setCreatedAt($date);
            $commande->setTransporteurName($transporteurs->getName());
            $commande->setTransporteurPrice($transporteurs->getPrice());
            $commande->setDelivery($delivery_content);
            $commande->setIsPaid(0);

            $this->em->persist($commande);

            foreach($panierService->getFull() as $produit) {
                $commandeDetails = new CommandeDetails();
                $commandeDetails->setCommande($commande);
                $commandeDetails->setProduit($produit['produit']->getName());
                $commandeDetails->setQuantity($produit['quantity']);
                $commandeDetails->setPrice($produit['produit']->getPrice());
                $commandeDetails->setTotal($produit['produit']->getPrice() * $produit['quantity']);
                $this->em->persist($commandeDetails);
            }
            
            $this->em->flush();

            return $this->render('commande/add.html.twig', [
                'transporteur' => $transporteurs, 
                'panier' => $panierService->getFull(),
                'delivery' => $delivery_content
            ]);

        }
        return $this->redirectToRoute('panier_index');
    }
}
