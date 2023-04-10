<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    const TMDB_BASE_URL = 'https://api.themoviedb.org/3/';
    const TMDB_SEARCH_MOVIE_ENDPOINT = 'search/movie';
    const TMDB_MOVIE_DETAIL_ENDPOINT = 'movie/';

    public function __construct(
        private readonly string $tmdbApiKey,
        private HttpClientInterface $httpClient,
    )
    {
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

    public function getMovieDetails(int $tmdbId): array {
        $url = self::TMDB_BASE_URL . self::TMDB_MOVIE_DETAIL_ENDPOINT . $tmdbId;

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