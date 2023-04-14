<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserMovieMeta;
use App\Repository\UserMovieMetaRepository;
use App\Service\MoviePersistenceService;
use App\Service\TmdbService;
use App\Service\UserMovieMetaFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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

    public function publicDiscover(int $page): JsonResponse
    {
        $date = new \DateTime();

        $movies = $this->tmdbService->discover($date, $page);

        $moviesResultSet = [];

        foreach ($movies['results'] as $movie) {
            $moviesResultSet[] = $this->moviePersistenceService->get($movie['id']);
        }

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_discover',
            'results' => $moviesResultSet,
        ]);

    }
    public function publicSearch(int $page, Request $request): JsonResponse
    {

        $searchTerm = $request->query->get('searchTerm');

        if (!$searchTerm) {
            return $this->json([
                'status' => 'error',
                'detail' => 'Empty searchTerm',
            ]);
        }

        $movies = $this->tmdbService->searchMovie($searchTerm, $page);

        $moviesResultSet = [];

        foreach ($movies['results'] as $movie) {
            $moviesResultSet[] = $this->moviePersistenceService->get($movie['id']);
        }

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_search',
            'searchTerm' => $searchTerm,
            'results' => $moviesResultSet,
        ]);
    }

    public function get(int $id, #[CurrentUser] ?User $user): JsonResponse
    {
        $movie = $this->moviePersistenceService->get($id);

        /*$movie->getUserMovieMetas()->filter(function(UserMovieMeta $userMovieMeta) use ($user) {
            return $userMovieMeta->getUser() === $user;
        });*/

        return $this->json([
            'status' => 'success',
            'detail' => 'movie_get',
            'id' => $id,
            'result' => $movie,
        ]);
    }

    public function addMeta(int $id, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $movie = $this->moviePersistenceService->get($id);

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
            'id' => $id,
            'result' => $userMovieMeta,
        ]);
    }
}
