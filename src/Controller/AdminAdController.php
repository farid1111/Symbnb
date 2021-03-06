<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Services\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminAdController extends Controller
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo,$page, PaginationService $pagination)
    {
        //Méthode find qui permet de retrouver un enregistrement par son identifiant
        $pagination->setEntityClass(Ad::class)
                   ->setPage($page);
        return $this->render('admin/ad/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }
    /**
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce a bien été modifiée"
            );
        }
        return $this->render('admin/ad/edit.html.twig', [
            'ad'=>$ad,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     */
    public function delete(Ad $ad,ObjectManager $manager)
    {
        if (count($ad->getBookings())>0) {
            $this->addFlash(
                'warning',
                "Suppession impossible"
            );
        }else{
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce a bien été supprimée"
            );
            return $this->redirectToRoute('admin_ads_index');
        }
    }
}
