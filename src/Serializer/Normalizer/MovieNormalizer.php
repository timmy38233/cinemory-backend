<?php

namespace App\Serializer\Normalizer;

use App\Entity\Movie;
use App\Entity\UserMovieMeta;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MovieNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private ObjectNormalizer $normalizer,
        private Security $security,
        private UserMovieMetaNormalizer $userMovieMetaNormalizer,
    )
    {
    }

    /** @param Movie $object */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $user = $this->security->getUser();

        $userMovieMetas = $object->getUserMovieMetas()->filter(function(UserMovieMeta $userMovieMeta) use ($user) {
           return $userMovieMeta->getUser() === $user;
        });

        foreach ($userMovieMetas as $userMovieMeta)
        {
            $data['userMovieMetas'][] = $this->userMovieMetaNormalizer->normalize($userMovieMeta, null, []);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Movie;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
