<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Normalizer;

use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;
use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\TypeAwareInterface;

class PlainNormalizer implements NormalizerInterface, MixedTypeDenormalizerInterface, TypeAwareInterface
{
    const KEY = 'plain';

    public function normalize($entity, NormalizationContext $normalizationContext)
    {
        return $entity;
    }

    public function denormalize($input, DenormalizationContext $context)
    {
        return $input;
    }

    public function getType(): string
    {
        return self::KEY;
    }
}
