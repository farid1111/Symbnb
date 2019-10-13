<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Services\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminCommentController extends Controller
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo,$page,PaginationService $pagination)
    {
        //Méthode find qui permet de retrouver un enregistrement par son identifiant
        $pagination->setEntityClass(Comment::class)
                   ->setLimit(5)
                   ->setPage($page)
                   ->setRoute('admin_ads_index');
        return $this->render('admin/comment/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }
    /**
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     */
    public function edit(Comment $comment, Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce a bien été modifiée"
            );
        }
        return $this->render('admin/comment/edit.html.twig', [
            'comment'=>$comment,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     */
    public function delete(Comment $comment,ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire a bien été supprimée"
        );
        return $this->redirectToRoute('admin_comments_index');
    }
}
