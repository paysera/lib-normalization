<?php

namespace Paysera\Component\Normalization;

interface NormalizerRegistryProviderInterface
{
    public function getDefaultNormalizerRegistry(): NormalizerRegistryInterface;

    /**
     * @param string|null $normalizationGroup Always pass an argument – will be changed to `?string`
     * @return NormalizerRegistryInterface
     */
    public function getNormalizerRegistryForNormalizationGroup(string $normalizationGroup = null): NormalizerRegistryInterface;
}
