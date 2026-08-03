<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Form\DevicesType;
use App\Repository\DevicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/devices')]
final class DevicesController extends AbstractController
{
    #[Route(name: 'app_devices_index', methods: ['GET'])]
    public function index(DevicesRepository $devicesRepository): Response
    {
        return $this->render('devices/index.html.twig', [
            'devices' => $devicesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_devices_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $device = new Devices();
        $form = $this->createForm(DevicesType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $device->setAverageRating(0.0);
            $entityManager->persist($device);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The device has been added successfully.'
            );
            return $this->redirectToRoute('app_devices_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devices/new.html.twig', [
            'device' => $device,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devices_show', methods: ['GET'])]
    public function show(Devices $device): Response
    {
        return $this->render('devices/show.html.twig', [
            'device' => $device,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devices_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devices $device, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevicesType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The device has been updated successfully.'
            );
            return $this->redirectToRoute('app_devices_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devices/edit.html.twig', [
            'device' => $device,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devices_delete', methods: ['POST'])]
    public function delete(Request $request, Devices $device, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$device->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($device);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The device has been deleted successfully.'
            );
        }

        return $this->redirectToRoute('app_devices_index', [], Response::HTTP_SEE_OTHER);
    }
}
