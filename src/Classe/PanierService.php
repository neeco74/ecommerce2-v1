<?php

namespace App\Classe;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class PanierService
{
    protected $session;
    protected $pr;
    


    public function __construct(SessionInterface $session, ProduitsRepository $pr, EntityManagerInterface $entityManager) {

            $this->session = $session;
            $this->pr = $pr;
            $this->em = $entityManager;
           
    }


    public function add($id)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])) {
            $panier[$id]++;
        }
        else {
            $panier[$id] = 1;
        }


        $this->session->set('panier', $panier);

        
    }

    public function get()
    {

        return $this->session->get('panier');

        
    }
    public function remove() 
    {

        return $this->session->remove('panier');

        
    }
    public function delete($id)
    {

        $panier = $this->session->get('panier', []);

        unset($panier[$id]);

        return $this->session->set('panier', $panier);
    }




    public function decrementer($id) {

        $panier = $this->get();
        
        if($panier[$id] > 1) {
            $panier[$id]--;
           
        }
        else {
            unset($panier[$id]);


        }
        $this->session->set('panier', $panier);
    }


    public function total() {

        $panier = $this->get();
        
        $total = 0;
        
        if($panier) {
            foreach($panier as $id => $qty) {

                $produit = $this->pr->find($id);
                if(!$produit) {
                    continue;

                }
                $total += $produit->getPrice() * $qty;
            }
        }
        
        
        return $total;
        
    }

    public function getFull() {

        $panierComplet = [];

        $panier = $this->get();
        
        if($panier) {
            foreach($panier as $id => $quantity) {

                $product_object = $this->em->getRepository(Produits::class)->findOneById($id);

                if(!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $panierComplet[] = [
                    'produit' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $panierComplet;
        
    }


}