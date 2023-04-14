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
     * @param UserMovieMeta $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, null, $context);

        $data['user'] = $object->getUser()->getUserIdentifier();
        $data['movie'] = $object->getMovie()->getId();

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
