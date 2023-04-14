<?php

namespace App\Serializer\Normalizer;

use App\Entity\Movie;
use App\Entity\WatchList;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class WatchListNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer)
    {
    }

    /** @param WatchList $object */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['user'] = $object->getUser()->getUserIdentifier();

        $data['movies'] = $object->getMovies()->map(function($movie) {
            /** @var Movie $movie */
            return $movie->getId();
        })->toArray();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\WatchList;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
