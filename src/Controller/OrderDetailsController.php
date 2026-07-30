<?php

namespace App\Controller;

use App\Entity\OrderDetails;
use App\Form\OrderDetailsType;
use App\Repository\OrderDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order/details')]
final class OrderDetailsController extends AbstractController
{
    #[Route(name: 'app_order_details_index', methods: ['GET'])]
    public function index(OrderDetailsRepository $orderDetailsRepository): Response
    {
        return $this->render('order_details/index.html.twig', [
            'order_details' => $orderDetailsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_details_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderDetail = new OrderDetails();
        $form = $this->createForm(OrderDetailsType::class, $orderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderDetail);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order_details/new.html.twig', [
            'order_detail' => $orderDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_details_show', methods: ['GET'])]
    public function show(OrderDetails $orderDetail): Response
    {
        return $this->render('order_details/show.html.twig', [
            'order_detail' => $orderDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_details_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrderDetails $orderDetail, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderDetailsType::class, $orderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order_details/edit.html.twig', [
            'order_detail' => $orderDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_details_delete', methods: ['POST'])]
    public function delete(Request $request, OrderDetails $orderDetail, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderDetail->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($orderDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_details_index', [], Response::HTTP_SEE_OTHER);
    }
}
