<?php

namespace App\Infrastructure\Shared\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Domain\User\Exception\UserNotFoundException;

class ExceptionNormalizer implements NormalizerInterface
{
    public function normalize($exception, $format = null, array $context = []): array
    {
        return [
            'message' => $exception->getMessage(),
        ];
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof UserNotFoundException;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            UserNotFoundException::class => true,
        ];
    }
}
