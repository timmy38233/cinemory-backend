<?php

namespace App\Controller;

use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private TmdbService $tmdbService
    ) {

    }
    public function search(Request $request): JsonResponse
    {

        $searchTerm = $request->query->get('searchTerm');

        if (!$searchTerm) {
            return $this->json([
                'status' => 'error',
                'detail' => 'Empty searchTerm',
            ]);
        }

        $movies = $this->tmdbService->searchMovie($searchTerm);
        
        return $this->json([
            'status' => 'success',
            'detail' => 'movie_search',
            'searchTerm' => $searchTerm,
            'results' => $movies,
        ]);
    }
}
