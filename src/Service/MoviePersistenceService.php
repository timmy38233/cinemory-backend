<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MoviePersistenceService
{
    public function __construct(
        private TmdbService $tmdbService,
        private MovieRepository $movieRepository,
    )
    {

    }

    public function get(int $id): Movie
    {
        $movie = $this->movieRepository->find($id);

        if (!$movie) {
            $movieData = $this->tmdbService->getMovieDetails($id);
            $movie = $this->saveFromApi($movieData);
        }

        return $movie;
    }

    private function saveFromApi(array $movieData): Movie
    {
        $releaseDate = \DateTime::createFromFormat("Y-m-d", $movieData['release_date']);

        $movie = (new Movie())
            ->setId($movieData['id'])
            ->setTitle($movieData['title'])
            ->setOriginalTitle($movieData['original_title'])
            ->setOriginalLanguage($movieData['original_language'])
            ->setOverview($movieData['overview'])
            ->setReleaseDate(($releaseDate instanceof \DateTimeInterface) ? $releaseDate : null)
            ->setPosterPath($movieData['poster_path'])
            ->setRuntime($movieData['runtime'])
            ->setReleaseStatus($movieData['status'])
            ->setImdbId($movieData['imdb_id']);

        $this->movieRepository->save($movie, true);

        return $movie;
    }


}