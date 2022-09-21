<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Controller;

use App\Core\Model\PaginatedRequestConfiguration;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking', name: 'booking_')]
class BookingController extends AbstractController
{
    public function __construct(private BookingRepository $bookingRepository)
    {
    }

    #[Route('/', name: 'list')]
    public function list(PaginatedRequestConfiguration $requestConfiguration): JsonResponse
    {
        $paginator = $this->bookingRepository->findAllByPaginatedRequest($requestConfiguration);

        return $this->json($paginator, Response::HTTP_OK, [], ['groups' => 'show']);
    }
}
