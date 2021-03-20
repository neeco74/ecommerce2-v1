<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Produits;
use App\Entity\Commandes;
use Stripe\Checkout\Session;
use App\Classe\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $em, PanierService $panierService, $reference): Response
    {

        $produits_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $commande = $em->getRepository(Commandes::class)->findOneByReference($reference);

        if(!$commande) {
            new JsonResponse(['error' => 'commande']);
        }


        
        foreach($commande->getCommandeDetails()->getValues() as $produit) {

            $produit_objet = $em->getRepository(Produits::class)->findOneByName($produit->getProduit());

            $produits_for_stripe[] = [
                'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $produit->getPrice(),
                'product_data' => [
                'name' => $produit->getProduit(),
                'images' => [$YOUR_DOMAIN."/uploads/".$produit_objet->getIllustration()],
                ],
            ],
            'quantity' => $produit->getQuantity(),
            ]; 
        }

        $produits_for_stripe[] = [
            'price_data' => [
            'currency' => 'eur',
            'unit_amount' => $commande->getTransporteurPrice(),
            'product_data' => [
            'name' => $commande->getTransporteurName(),
            'images' => [$YOUR_DOMAIN],
            ],
        ],
        'quantity' => 1,
        ]; 


       

        \Stripe\Stripe::setApiKey('sk_test_51IWlKzFjn869IRqbQeU4CM9XQ0Xsw6coXZdCmJ5He7IpQquOjMlNLLURdqIKGhLFLFAn4c1KG7QIBVcmtDCmmFP700mQBhU75z');


        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $produits_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $commande->setStripeSessionId($checkout_session->id);

        $em->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response;
    }
}
