<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserMovieMetaRepository;
use App\Service\MoviePersistenceService;
use App\Service\TmdbService;
use App\Service\UserMovieMetaFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MovieController extends AbstractController
{
    public function __construct(
        private TmdbService             $tmdbService,
        private MoviePersistenceService $moviePersistenceService,
        private UserMovieMetaFactory    $userMovieMetaFactory,
        private UserMovieMetaRepository $userMovieMetaRepository,
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

    public function get(int $tmdbId, #[CurrentUser] ?User $user): JsonResponse
    {
        $movie = $this->moviePersistenceService->get($tmdbId);

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_get',
            'tmdbId' => $tmdbId,
            'result' => $movie,
        ]);
    }

    public function addMeta(int $tmdbId, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $movie = $this->moviePersistenceService->get($tmdbId);

        $userNotes = json_decode($request->getContent(), true);

        $userMovieMeta = $this->userMovieMetaRepository->findOneBy([
                'user' => $user,
                'movie' => $movie]
        );

        if (!$userMovieMeta) {
            $userMovieMeta = $this->userMovieMetaFactory->createUserMovieMeta($user, $movie);
        }

        $userMovieMeta->setNotes($userNotes['notes']);

        $this->userMovieMetaRepository->save($userMovieMeta, true);

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_add_meta',
            'tmdbId' => $tmdbId,
            'result' => $userMovieMeta,
        ]);
    }
}
