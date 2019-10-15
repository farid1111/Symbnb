<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    // /**
    //  * @Route("/", name="blog")
    //  */
    // public function index()
    // {
        
    //     return $this->render('blog/index.html.twig');
    // }
    /**
     * @Route("/", name="home")
     */
    public function home(AdRepository $adRepo,UserRepository $userRepo){
        return $this->render('home.html.twig',[
            'ads'=>$adRepo->findBestAds(3),
            'users'=> $userRepo->findBestUsers(3)
        ]);

    }
}