<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Normalizer;

use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\TypeAwareInterface;

class InnerDataNormalizer implements NormalizerInterface, TypeAwareInterface
{

    /**
     * @param InnerData $data
     * @param NormalizationContext $normalizationContext
     *
     * @return mixed
     */
    public function normalize($data, NormalizationContext $normalizationContext)
    {
        return [
            'inner_property' => $data->getProperty(),
            'optional_property' => $normalizationContext->isFieldExplicitlyIncluded('optional_property')
                ? $data->getOptionalProperty()
                : null,
        ];
    }

    public function getType(): string
    {
        return InnerData::class;
    }
}
