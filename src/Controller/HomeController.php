<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

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
    public function home(){
        return $this->render('home.html.twig');

    }
}