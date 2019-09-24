<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;

interface NormalizerRegistryInterface
{
    const DENORMALIZER_TYPE_OBJECT = 'object';
    const DENORMALIZER_TYPE_MIXED = 'mixed';
    const DENORMALIZER_TYPE_NONE = 'none';

    /**
     * @param string $type
     * @return NormalizerInterface
     * @throws NormalizerNotFoundException
     */
    public function getNormalizer(string $type): NormalizerInterface;

    /**
     * @param string $type
     * @return bool
     */
    public function hasNormalizer(string $type): bool;

    /**
     * @param string $type
     * @return ObjectDenormalizerInterface
     * @throws NormalizerNotFoundException
     */
    public function getObjectDenormalizer(string $type): ObjectDenormalizerInterface;

    /**
     * @param string $type
     * @return MixedTypeDenormalizerInterface
     * @throws NormalizerNotFoundException
     */
    public function getMixedTypeDenormalizer(string $type): MixedTypeDenormalizerInterface;

    /**
     * @param string $type
     * @return string one of DENORMALIZER_TYPE_* constants
     */
    public function getDenormalizerType(string $type): string;
}
