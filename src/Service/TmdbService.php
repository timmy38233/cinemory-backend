<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    const TMDB_BASE_URL = 'https://api.themoviedb.org/3/';
    const TMDB_DISCOVER_MOVIE = 'discover/movie';
    const TMDB_SEARCH_MOVIE_ENDPOINT = 'search/movie';
    const TMDB_MOVIE_DETAIL_ENDPOINT = 'movie/';

    public function __construct(
        private readonly string $tmdbApiKey,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function discover(\DateTime $date, $page): array
    {
        $url = self::TMDB_BASE_URL . self::TMDB_DISCOVER_MOVIE;

        $response = $this->httpClient->request('GET', $url,
        [
            'query' => [
                'api_key' => $this->tmdbApiKey,
                'region' => 'DE',
                'language' => 'DE',
                'primary_release_date.gte' => $date->format('Y-m-d'),
                'page' => $page,
            ],
        ]);

        return $response->toArray();
    }

    public function searchMovie(string $searchTerm, int $page): array
    {
        $url = self::TMDB_BASE_URL . self::TMDB_SEARCH_MOVIE_ENDPOINT;

        $response = $this->httpClient->request('GET', $url,
            [
                'query' => [
                    'api_key' => $this->tmdbApiKey,
                    'region' => 'DE',
                    'language' => 'DE',
                    'query' => $searchTerm,
                    'page' => $page,
                ]
            ]);

        return $response->toArray();

    }

    public function getMovieDetails(int $id): array {
        $url = self::TMDB_BASE_URL . self::TMDB_MOVIE_DETAIL_ENDPOINT . $id;

        $response = $this->httpClient->request('GET', $url,
            [
                'query' => [
                    'api_key' => $this->tmdbApiKey,
                    'language' => 'DE',
                ]
            ]);

        return $response->toArray();
    }
}