<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\AdminBookingType;
use App\Services\PaginationService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminBookingController extends Controller
{
     /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo,$page,PaginationService $pagination)
    {
        //Méthode find qui permet de retrouver un enregistrement par son identifiant
        $pagination->setEntityClass(Booking::class)
                   ->setPage($page)
                   ->setRoute('admin_bookings_index');
        return $this->render('admin/booking/index.html.twig', [
            'pagination'=>$pagination
        ]);
    }
    /**
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     */
    public function edit(Booking $booking, Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdminBookingType::class, $booking,[
            'validation_groups'=>["Default"]
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La reservation a bien été modifiée"
            );
            return $this->redirectToRoute("admin_bookings_index");
        }
        return $this->render('admin/booking/edit.html.twig', [
            'bookings'=>$booking,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     */
    public function delete(Booking $booking,ObjectManager $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le bookingaire a bien été supprimée"
        );
        return $this->redirectToRoute('admin_bookings_index');
    }
}
