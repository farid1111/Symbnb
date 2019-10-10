<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/inscription", name="account_register")
     */
    public function registration(Request $request,ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user= new User();
        $form=$this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été enregistré! Vous pouvez maintenant vous connectez"
            );

            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils){
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError'=> $error !== null,
            'username'=>$username
        ]);
    }
    /**
     * @Route("/deconnexion", name="account_logout")
     * 
     * @return void
     */
    public function logout(){}
    
    /**
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(Request $request,ObjectManager $manager){
        $user=$this->getUser();
        $form=$this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont été modifiées avec succés"
            );

            return $this->redirectToRoute('account_profile');
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager){
        $passwordUpdate= new PasswordUpdate();
        $user = $this->getUser();
        $form=$this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(),$user->getHash())) {
                $form->get('oldPassword')->addError(new formError("mauvais mot de passe !"));
            }else {
                $newPassword= $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);
                $user->setHash($hash); 
                $manager->persist($passwordUpdate);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Les données du profil ont été modifiées avec succés"
                );
                return $this->redirectToRoute('homepage');
            }
        }   


        //     return $this->redirectToRoute('account_profile');
        // }
        return $this->render('account/update_password.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
    /**
     * @Route("/account", name="account_index")
     * 
     * @return Response
     */
    public function myAccount(){  
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
        
    }
        /**
     * @Route("/account/bookings", name="account_bookings")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function bookings(){
        return $this->render('account/bookings.html.twig');
        
    }   


}
