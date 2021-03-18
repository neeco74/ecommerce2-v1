<?php

namespace App\Controller;

use App\Entity\Address;
use App\Classe\PanierService;
use App\Form\AddressFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/compte/adresses/add", name="account_address_add")
     */
    public function add(PanierService $panierService, Request $request): Response
    {

        $address = new Address();
        $form = $this->createForm(AddressFormType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            if($panierService->get()) {

                return $this->redirectToRoute('commande_index');
            }

            return $this->redirectToRoute('account_address');

        }


        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresses/edit/{id}", name="account_address_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->find($id);

        if(!$address OR $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }


        
        $form = $this->createForm(AddressFormType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // $address->setUser($this->getUser());

            // $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('account_address');

        }


        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresses/delete/{id}", name="account_address_delete")
     */
    public function delete($id)
    {
        $address = $this->em->getRepository(Address::class)->find($id);

        if($address && $address->getUser() == $this->getUser()) {
            $this->em->remove($address);
            
            $this->em->flush();
        }

        return $this->redirectToRoute('account_address');
    }
}
