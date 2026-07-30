<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Devices;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Rating;
use App\Form\SelectCustomerType;
use App\Repository\DevicesRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class UserViewController extends AbstractController
{
    #[Route('/user/view', name: 'app_user_view')]
    public function index(DevicesRepository $devicesRepository): Response
    {
        // Lấy danh sách tất cả các Devices từ cơ sở dữ liệu
        $devices = $devicesRepository->findAll();

        return $this->render('user_view/index.html.twig', [
            'devices' => $devices,
        ]);
    }

    #[Route('/user/view/{id}', name: 'user_device_show', methods: ['GET'])]
    public function show(
        int $id,
        DevicesRepository $devicesRepository,
        RatingRepository $ratingRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Lấy thông tin Device
        $device = $devicesRepository->find($id);
        if (!$device) {
            throw $this->createNotFoundException('Device not found');
        }
        $comments = $device->getComments();
        if (!$device) {
            throw $this->createNotFoundException(
                'The device does not exist.');
        }

        // Tính toán Average Rating
        $averageRating = $ratingRepository->calculateAverageRating(
            $device->getId());

        // Cập nhật Average Rating vào Device
        $device->setAverageRating($averageRating);
        $entityManager->persist($device);
        $entityManager->flush();

        foreach ($comments as $comment) {
            if ($comment instanceof Comment) {
                $comment->setDevice($device);
                echo $comment->getContent();
            }
        }

        // Truyền thông tin vào view
        return $this->render('user_view/show.html.twig', [
            'device' => $device,
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/user/view/{id}/add-to-cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DevicesRepository $devicesRepository
    ): Response {
        // Lấy thông tin Device
        $device = $devicesRepository->find($id);
        if (!$device) {
            throw $this->createNotFoundException('The device does not exist.');
        }

        // Tạo form chọn Customer
        $form = $this->createForm(SelectCustomerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Lấy thông tin Customer từ form
            $customer = $form->get('Customer')->getData();

            // Tạo Order mới
            $order = new Order();
            $order->setOrderDate(new \DateTime());
            $order->setCustomer($customer);
            $order->setTotalAmount($device->getPrice()); // Tổng tiền ban đầu là giá của Device
            $entityManager->persist($order);

            // Tạo OrderDetails mới
            $orderDetail = new OrderDetails();
            $orderDetail->setOrder($order);
            $orderDetail->setDevice($device);
            $orderDetail->setQuantity(1); // Mặc định số lượng là 1
            $orderDetail->setPrice($device->getPrice());
            $orderDetail->setAmount($device->getPrice()); // Tổng tiền = giá * số lượng
            $entityManager->persist($orderDetail);

            // Lưu vào cơ sở dữ liệu
            $entityManager->flush();

            // Thông báo thành công
            $this->addFlash('success', 'Device added to cart successfully!');
            return $this->redirectToRoute('user_device_show', ['id' => $id]);
        }

        return $this->render('user_view/add_to_cart.html.twig', [
            'device' => $device,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/view/{id}/rate', name: 'rate_device', methods: ['POST'])]
    public function rateDevice(
        int $id,
        Request $request,
        DevicesRepository $devicesRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Lấy thông tin Device
        $device = $devicesRepository->find($id);
        if (!$device) {
            throw $this->createNotFoundException('The device does not exist.');
        }
        // Lấy rating từ request
        $ratingValue = (int) $request->request->get('rating');
        if ($ratingValue < 1 || $ratingValue > 5) {
            $this->addFlash('error', 'Invalid rating value.');
            return $this->redirectToRoute('user_device_show', ['id' => $id]);
        }
        // Tạo một Rating mới
        $rating = new Rating();
        $rating->setDeviceId($device->getId());
        $rating->setPoints($ratingValue);
        $rating->setDateCreated(new \DateTime());
        // Lưu vào cơ sở dữ liệu
        $entityManager->persist($rating);
        $entityManager->flush();
        // Thông báo thành công
        $this->addFlash('success', 'Thank you for your rating!');
        return $this->redirectToRoute('user_device_show', ['id' => $id]);
    }

    #[Route('/user/view/{id}/comment', name: 'add_comment', methods: ['POST'])]
    public function addComment(
        int $id,
        Request $request,
        DevicesRepository $devicesRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Lấy thông tin Device
        $device = $devicesRepository->find($id);
        if (!$device) {
            throw $this->createNotFoundException('The device does not exist.');
        }
        // Lấy nội dung comment từ request
        $commentContent = $request->request->get('comment');
        if (empty($commentContent)) {
            $this->addFlash('error', 'Comment cannot be empty.');
            return $this->redirectToRoute('user_device_show', ['id' => $id]);
        }
        // Tạo một Comment mới
        $comment = new Comment();
        $comment->setDevice($device);
        $comment->setContent($commentContent);
        $comment->setPosition(null); // Nếu bạn muốn quản lý vị trí, có thể cập nhật logic này
        $comment->setParentComment(null); // Mặc định là bình luận gốc (không có parent)
        $comment->setCreatedAt(new \DateTime());
        // Lưu vào cơ sở dữ liệu
        $entityManager->persist($comment);
        $entityManager->flush();
        // Thông báo thành công
        $this->addFlash('success', 'Your comment has been added!');
        return $this->redirectToRoute('user_device_show', ['id' => $id]);
    }
}
