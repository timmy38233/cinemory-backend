<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WatchList;
use App\Repository\WatchListRepository;
use App\Service\MoviePersistenceService;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

class WatchListController extends AbstractController
{
    public function __construct(
        private WatchListRepository     $watchListRepository,
        private MoviePersistenceService $moviePersistenceService,
        private SluggerInterface        $slugger,
    )
    {
    }

    public function list(#[CurrentUser] ?User $user): JsonResponse
    {
        /** @var Collection<int, WatchList> $watchLists */
        $watchLists = $user->getWatchLists();

        return $this->json([
            'status' => 'success',
            'detail' => 'watchlist_list',
            'result' => [$watchLists],
        ]);
    }

    public function new(Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $title = $this->slugger->slug($jsonData['title']);

        $watchList = $this->watchListRepository->findOneBy([
            'user' => $user,
            'title' => $title
        ]);

        if (!$watchList) {
            $watchList = (new WatchList())
                ->setTitle($title)
                ->setUser($user);

            $this->watchListRepository->save($watchList, true);

            return $this->json([
                'status' => 'success',
                'detail' => 'watchlist_new',
                'result' => [
                    'id' => $watchList->getId(),
                    'title' => $watchList->getTitle(),
                    'user' => $watchList->getUser()->getUserIdentifier(),
                ],
            ]);
        }

        return $this->json([
            'status' => 'error',
            'detail' => 'watchlist_new::already_exists',
            'result' => [
                'id' => $watchList->getId(),
                'title' => $watchList->getTitle(),
                'user' => $watchList->getUser()->getUserIdentifier(),
            ],
        ], Response::HTTP_BAD_REQUEST);
    }

    public function addMovie(int $watchListId, Request $request, #[CurrentUser] ?User $user)
    {
        $watchList = $this->watchListRepository->findOneBy([
            'id' => $watchListId,
            'user' => $user
        ]);

        $jsonData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $movie = $this->moviePersistenceService->get($jsonData['tmdbId']);

        if ($watchList->getMovies()->contains($movie)) {
            return $this->json([
                'status' => 'error',
                'detail' => 'watchlist_add_movie::duplicate',
                'result' => [
                    'id' => $watchList->getId(),
                    'title' => $watchList->getTitle(),
                    'user' => $watchList->getUser()->getUserIdentifier(),
                    'movie' => $movie->getTmdbId(),
                ]
            ]);
        }
        $watchList->addMovie($movie);

        $this->watchListRepository->save($watchList, true);

        return $this->json([
            'status' => 'success',
            'detail' => 'watchlist_add_movie',
            'result' => [
                'id' => $watchList->getId(),
                'title' => $watchList->getTitle(),
                'user' => $watchList->getUser()->getUserIdentifier(),
                'movie' => $movie->getTmdbId(),
            ]
        ]);
    }

    public function getMovies(int $watchListId, #[CurrentUser] ?User $user)
    {
        $watchList = $this->watchListRepository->findOneBy([
            'id' => $watchListId,
            'user' => $user
        ]);

        return $this->json([
            'status' => 'success',
            'detail' => 'watchlist_get_movies',
            'result' => $watchList,
        ]);
    }

    public function removeMovie(int $watchListId, Request $request, #[CurrentUser] ?User $user)
    {
        $watchList = $this->watchListRepository->findOneBy([
            'id' => $watchListId,
            'user' => $user
        ]);

        $jsonData = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $movie = $this->moviePersistenceService->get($jsonData['tmdbId']);

        if (!$watchList->getMovies()->contains($movie)) {
            return $this->json([
                'status' => 'error',
                'detail' => 'watchlist_remove_movie::not_existing',
                'result' => [
                    'id' => $watchList->getId(),
                    'title' => $watchList->getTitle(),
                    'user' => $watchList->getUser()->getUserIdentifier(),
                    'movie' => $movie->getTmdbId(),
                ]
            ]);
        }
        $watchList->removeMovie($movie);

        $this->watchListRepository->save($watchList, true);

        return $this->json([
            'status' => 'success',
            'detail' => 'watchlist_remove_movie',
            'result' => [
                'id' => $watchList->getId(),
                'title' => $watchList->getTitle(),
                'user' => $watchList->getUser()->getUserIdentifier(),
                'movie' => $movie->getTmdbId(),
            ]
        ]);
    }
}
