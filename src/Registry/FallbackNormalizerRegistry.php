<?php

namespace Paysera\Component\Normalization\Registry;

use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;
use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\NormalizerRegistryInterface;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;

/**
 * @internal
 */
class FallbackNormalizerRegistry implements NormalizerRegistryInterface
{
    private $mainRegistry;
    private $fallbackRegistry;

    public function __construct(
        NormalizerRegistryInterface $mainRegistry,
        NormalizerRegistryInterface $fallbackRegistry
    ) {
        $this->mainRegistry = $mainRegistry;
        $this->fallbackRegistry = $fallbackRegistry;
    }

    public function getNormalizer(string $type): NormalizerInterface
    {
        try {
            return $this->mainRegistry->getNormalizer($type);
        } catch (NormalizerNotFoundException $exception) {
            return $this->fallbackRegistry->getNormalizer($type);
        }
    }

    public function hasNormalizer(string $type): bool
    {
        return $this->mainRegistry->hasNormalizer($type) || $this->fallbackRegistry->hasNormalizer($type);
    }

    public function getObjectDenormalizer(string $type): ObjectDenormalizerInterface
    {
        try {
            return $this->mainRegistry->getObjectDenormalizer($type);
        } catch (NormalizerNotFoundException $exception) {
            return $this->fallbackRegistry->getObjectDenormalizer($type);
        }
    }

    public function getMixedTypeDenormalizer(string $type): MixedTypeDenormalizerInterface
    {
        try {
            return $this->mainRegistry->getMixedTypeDenormalizer($type);
        } catch (NormalizerNotFoundException $exception) {
            return $this->fallbackRegistry->getMixedTypeDenormalizer($type);
        }
    }

    public function getDenormalizerType(string $type): string
    {
        $denormalizerType = $this->mainRegistry->getDenormalizerType($type);
        if ($denormalizerType === self::DENORMALIZER_TYPE_NONE) {
            $denormalizerType = $this->fallbackRegistry->getDenormalizerType($type);
        }

        return $denormalizerType;
    }
}
