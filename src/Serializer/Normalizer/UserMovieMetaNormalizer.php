<?php

namespace App\Serializer\Normalizer;

use App\Entity\UserMovieMeta;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserMovieMetaNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer)
    {
    }

    /**
     * @param UserMovieMeta $userMovieMeta
     */
    public function normalize($userMovieMeta, string $format = null, array $context = []): array
    {
        $data = array();
        $data['user'] = $userMovieMeta->getUser()->getUserIdentifier();
        $data['movie'] = $userMovieMeta->getMovie()->getTmdbId();
        $data['notes'] = $userMovieMeta->getNotes();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\UserMovieMeta;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
