<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Denormalizer;

use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\TypeAwareInterface;
use Paysera\Component\ObjectWrapper\ObjectWrapper;

class InnerDataDenormalizer implements ObjectDenormalizerInterface, TypeAwareInterface
{
    public function getType(): string
    {
        return InnerData::class;
    }

    /**
     * @param ObjectWrapper $data
     * @param DenormalizationContext $context
     * @return InnerData
     */
    public function denormalize(ObjectWrapper $data, DenormalizationContext $context)
    {
        return (new InnerData())
            ->setProperty($data->getRequiredString('inner_property'))
        ;
    }
}
