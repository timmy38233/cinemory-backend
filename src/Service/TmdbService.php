<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    const TMDB_BASE_URL = 'https://api.themoviedb.org/3/';
    const TMDB_SEARCH_ENDPOINT = 'search/movie';

    public function __construct(
        private readonly string $tmdbApiKey,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function searchMovie(string $searchTerm): array
    {
        $url = self::TMDB_BASE_URL . self::TMDB_SEARCH_ENDPOINT;

        $response = $this->httpClient->request('GET', $url,
            [
                'query' => [
                    'api_key' => $this->tmdbApiKey,
                    'region' => 'DE',
                    'language' => 'DE',
                    'query' => $searchTerm,
                ]
            ]);

        return $response->toArray();

    }
}