<?php

namespace App\Controller;

use DateTime;
use App\Classe\MailJet;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;


    }
    
    
    /**
     * @Route("/forget-password", name="reset_password")
     */
    public function index(Request $request, UsersRepository $ur)
    {

        if($this->getUser()) {

            return $this->redirectToRoute('home');
        }

        if($request->get('email')) {

            $user = $ur->findOneByEmail($request->get('email'));

            if($user) {
                // Etape 1 : Enregistrer en base la demande de reset password avec user, token et createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \Datetime());
                $this->em->persist($reset_password);
                $this->em->flush();

                // 2 : ENvoyer un email à l'user avec un lien lui permettant de mettre à jour son mdp
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $mail = new MailJet();

                $content = "Bonjour ".$user->getFirstName()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur La boutique FR<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe</a>.";

                $mail->send($user->getEmail(), $user->getFirstName().' '.$user->getLastName(), "Réinitialiser votre mot de passe", $content);
                

                $this->addFlash('notice', 'Vous allez recevoir un email pour réinitialiser votre mot de passe.');

            }
            else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
  
            }


        }


        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/update-password/{token}", name="update_password")
     */
    public function update($token, ResetPasswordRepository $rpr, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $reset_password = $rpr->findOneByToken($token);

        if(!$reset_password) {
            return $this->redirectToRoute('reset_password');

        }
        
        // Verifier si le createdAt = now -3h
        $now = new \DateTime();
        if($now > ($reset_password->getCreatedAt()->modify('+ 3 hour'))) {
            // modifier mon mdp
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $new_password = $form->get('new_password')->getData();

            $password = $encoder->encodePassword($reset_password->getUser(), $new_password);

            $reset_password->getUser()->setPassword($password);

            $this->em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');

            return $this->redirectToRoute('app_login');

        }




        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
        // Rendre une vue avec mot de passe et confirmer mdp



    }
}
