<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Denormalizer;

use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;
use Paysera\Component\Normalization\TypeAwareInterface;
use Paysera\Component\ObjectWrapper\ObjectWrapper;

class MyDataDenormalizer implements ObjectDenormalizerInterface, TypeAwareInterface
{
    public function getType(): string
    {
        return MyData::class;
    }

    /**
     * @param ObjectWrapper $data
     * @param DenormalizationContext $context
     * @return mixed
     */
    public function denormalize(ObjectWrapper $data, DenormalizationContext $context)
    {
        return (new MyData())
            ->setProperty($data->getRequiredString('property'))
            ->setInnerData($context->denormalize($data->getRequiredObject('inner'), InnerData::class))
            ->setInnerDataList(
                $context->denormalizeArray($data->getArrayOfObject('inner_list'), InnerData::class)
            )
        ;
    }
}
