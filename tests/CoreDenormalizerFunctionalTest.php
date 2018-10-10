<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Paysera\Component\Normalization\CoreDenormalizer;
use Paysera\Component\Normalization\NormalizerRegistry;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\InnerDataDenormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\MyDataDenormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;
use Paysera\Component\ObjectWrapper\Exception\MissingItemException;
use PHPUnit\Framework\TestCase;

class CoreDenormalizerFunctionalTest extends TestCase
{
    public function testDenormalize()
    {
        $normalizerRegistry = new NormalizerRegistry();
        $normalizerRegistry->addObjectDenormalizer(new MyDataDenormalizer());
        $normalizerRegistry->addObjectDenormalizer(new InnerDataDenormalizer());

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistry);

        $object = (object)['property' => 'my_data', 'inner' => (object)['inner_property' => 'inner_data']];
        $expectedObject = (new MyData())->setProperty('my_data')->setInnerData(
            (new InnerData())->setProperty('inner_data')
        );
        $result = $coreDenormalizer->denormalize($object, MyData::class);
        $this->assertEquals($expectedObject, $result);

        $result = $coreDenormalizer->denormalize([$object, $object], MyData::class . '[]');
        $this->assertEquals([$expectedObject, $expectedObject], $result);
    }

    public function testDenormalizeWithMissingItem()
    {
        $normalizerRegistry = new NormalizerRegistry();
        $normalizerRegistry->addObjectDenormalizer(new MyDataDenormalizer());
        $normalizerRegistry->addObjectDenormalizer(new InnerDataDenormalizer());

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistry);

        $object = (object)['property' => 'my_data', 'inner' => (object)['other_prop' => 'something']];
        $this->expectExceptionObject(new MissingItemException('inner.inner_property'));
        $coreDenormalizer->denormalize($object, MyData::class);
    }

    public function testDenormalizeWithMissingItemInsideArray()
    {
        $normalizerRegistry = new NormalizerRegistry();
        $normalizerRegistry->addObjectDenormalizer(new MyDataDenormalizer());
        $normalizerRegistry->addObjectDenormalizer(new InnerDataDenormalizer());

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistry);

        $object = (object)[
            'property' => 'my_data',
            'inner' => (object)['inner_property' => 'inner_data'],
            'inner_list' => [(object)['other_prop' => 'something']],
        ];
        $this->expectExceptionObject(new MissingItemException('inner_list.0.inner_property'));
        $coreDenormalizer->denormalize($object, MyData::class);
    }
}
