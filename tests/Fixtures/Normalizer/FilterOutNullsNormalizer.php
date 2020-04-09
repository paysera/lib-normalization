<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Normalizer;

use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;

class FilterOutNullsNormalizer implements NormalizerInterface
{
    /**
     * @param MyData $data
     * @param NormalizationContext $normalizationContext
     *
     * @return mixed
     */
    public function normalize($data, NormalizationContext $normalizationContext)
    {
        $normalizationContext->markNullValuesForRemoval();
        return [
            'property' => $data->getProperty(),
            'inner' => $data->getInnerData(),
            'inner_list' => $data->getInnerDataList(),
        ];
    }
}
