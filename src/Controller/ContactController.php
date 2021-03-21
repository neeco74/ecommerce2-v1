<?php

namespace App\Controller;

use App\Classe\MailJet;
use App\Repository\HeadersRepository;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request)
    {

        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais');

            $mail = new MailJet();

            $content = null;

            $mail->send('olagnon.n@gmail.com', 'La boutique FR', "Vous avez reçu une demande de contact", $content);


        }
        return $this->render('home/index.html.twig', );
    }
}
