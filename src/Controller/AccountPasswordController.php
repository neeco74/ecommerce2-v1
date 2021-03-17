<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    /**
     * @Route("/compte/change-password", name="account_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, $user);

        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()) {

            $old_password = $form->get('old_password')->getData();

            if($encoder->isPasswordValid($user, $old_password)) {

                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $new_password);

                $user->setPassword($password);

           
                $this->em->persist($user);
                $this->em->flush();

                $notification = "Votre mot de passe a bien été mis à jour";
            }
            else {
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig', [
                'form' => $form->createView(),
                'notification' => $notification
        ]);
    }
}
