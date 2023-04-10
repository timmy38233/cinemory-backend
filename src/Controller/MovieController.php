<?php

namespace App\Controller;

use App\Service\MoviePersistenceService;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends AbstractController
{
    public function __construct(
        private TmdbService $tmdbService,
        private MoviePersistenceService $moviePersistenceService,
    )
    {

    }

    public function search(int $page, Request $request): JsonResponse
    {

        $searchTerm = $request->query->get('searchTerm');

        if (!$searchTerm) {
            return $this->json([
                'status' => 'error',
                'detail' => 'Empty searchTerm',
            ]);
        }

        $movies = $this->tmdbService->searchMovie($searchTerm, $page);

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_search',
            'searchTerm' => $searchTerm,
            'results' => $movies,
        ]);
    }

    public function get(int $tmdbId): JsonResponse
    {

        $movie = $this->moviePersistenceService->get($tmdbId);

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_get',
            'tmdbId' => $tmdbId,
            'result' => $movie,
        ]);

    }
}
