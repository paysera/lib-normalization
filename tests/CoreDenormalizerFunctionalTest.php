<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Mockery;
use Paysera\Component\Normalization\CoreDenormalizer;
use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;
use Paysera\Component\Normalization\Registry\GroupedNormalizerRegistryProvider;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\InnerDataDenormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\MyDataDenormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;
use Paysera\Component\ObjectWrapper\Exception\MissingItemException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

class CoreDenormalizerFunctionalTest extends MockeryTestCase
{
    public function testDenormalize()
    {
        $normalizerRegistry = new GroupedNormalizerRegistryProvider();
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
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();
        $normalizerRegistryProvider->addObjectDenormalizer(new MyDataDenormalizer());
        $normalizerRegistryProvider->addObjectDenormalizer(new InnerDataDenormalizer());

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistryProvider);

        $object = (object)['property' => 'my_data', 'inner' => (object)['other_prop' => 'something']];
        $this->expectExceptionObject(new MissingItemException('inner.inner_property'));
        $coreDenormalizer->denormalize($object, MyData::class);
    }

    public function testDenormalizeWithMissingItemInsideArray()
    {
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();
        $normalizerRegistryProvider->addObjectDenormalizer(new MyDataDenormalizer());
        $normalizerRegistryProvider->addObjectDenormalizer(new InnerDataDenormalizer());

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistryProvider);

        $object = (object)[
            'property' => 'my_data',
            'inner' => (object)['inner_property' => 'inner_data'],
            'inner_list' => [(object)['other_prop' => 'something']],
        ];
        $this->expectExceptionObject(new MissingItemException('inner_list.0.inner_property'));
        $coreDenormalizer->denormalize($object, MyData::class);
    }

    public function testDenormalizeWithDifferentGroups()
    {
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();

        $object = new stdClass();
        $denormalizer = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizer->shouldReceive('denormalize')->andReturn($object);

        $objectForA = new stdClass();
        $denormalizerForA = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizerForA->shouldReceive('denormalize')->andReturn($objectForA);

        $normalizerRegistryProvider->addObjectDenormalizer($denormalizer, 'type');
        $normalizerRegistryProvider->addObjectDenormalizer($denormalizerForA, 'type', 'groupA');

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistryProvider);
        $context = new DenormalizationContext($coreDenormalizer, 'groupA');
        $result = $coreDenormalizer->denormalize((object)[], 'type', $context);

        $this->assertSame($objectForA, $result);
    }
}
