<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Normalizer;

use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;

class WrappedNormalizer implements NormalizerInterface
{
    /**
     * @param MyData $data
     * @param NormalizationContext $normalizationContext
     *
     * @return mixed
     */
    public function normalize($data, NormalizationContext $normalizationContext)
    {
        return $normalizationContext->normalize($data, '');
    }
}
