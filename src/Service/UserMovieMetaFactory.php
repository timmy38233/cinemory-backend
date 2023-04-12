<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\UserMovieMeta;
use App\Repository\UserMovieMetaRepository;

class UserMovieMetaFactory
{

    public function __construct(
        private UserMovieMetaRepository $userMovieMetaRepository,
    )
    {
    }
    public function createUserMovieMeta(User $user, Movie $movie): UserMovieMeta
    {
        $userMovieMeta = (new UserMovieMeta())
            ->setUser($user)
            ->setMovie($movie);

        $this->userMovieMetaRepository->save($userMovieMeta, true);

        return $userMovieMeta;
    }
}