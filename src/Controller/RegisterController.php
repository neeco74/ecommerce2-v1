<?php

namespace App\Controller;

use App\Classe\MailJet;
use App\Entity\Users;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $notification = null;

        $user = new Users();

        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $search_email = $this->em->getRepository(Users::class)->findOneByEmail($user->getEmail());

            if(!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                
                $this->em->persist($user);
                $this->em->flush();

                $mail = new MailJet();
                $content = "Bonjour ".$user->getFirstName()."<br/>Bienvenue sur la première boutique dédiée au made in France.<br/><br/>";
                $mail->send($user->getEmail(), $user-getFirstName(), "Bienvenue sur la boutique FR", $content);


                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            }
            else {
                $notification = "L'email que vous avez renseigné existe déja.";


            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
