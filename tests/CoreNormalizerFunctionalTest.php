<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use ArrayIterator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Paysera\Component\Normalization\CoreNormalizer;
use Paysera\Component\Normalization\DataFilter;
use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\Registry\GroupedNormalizerRegistryProvider;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;
use Paysera\Component\Normalization\Tests\Fixtures\Normalizer\InnerDataNormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Normalizer\MyDataNormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Normalizer\WrappedNormalizer;
use Paysera\Component\Normalization\TypeGuesser;

class CoreNormalizerFunctionalTest extends MockeryTestCase
{
    public function testNormalize()
    {
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();
        $normalizerRegistryProvider->addNormalizer(new MyDataNormalizer());
        $normalizerRegistryProvider->addNormalizer(new InnerDataNormalizer());
        $normalizerRegistryProvider->addNormalizer(new WrappedNormalizer(), 'wrapped');

        $typeGuesser = new TypeGuesser();
        $dataFilter = new DataFilter();

        $coreNormalizer = new CoreNormalizer($normalizerRegistryProvider, $typeGuesser, $dataFilter);

        $result = $coreNormalizer->normalize((new MyData())->setProperty('my_data')->setInnerData(
            (new InnerData())->setProperty('inner_data')->setOptionalProperty('optional_value')
        )->setInnerDataList([
            (new InnerData())->setProperty('inner_data1')->setOptionalProperty('optional_value1'),
            (new InnerData())->setProperty('inner_data2'),
        ]));
        $this->assertEquals(
            (object)[
                'property' => 'my_data',
                'inner' => (object)['inner_property' => 'inner_data'],
                'inner_list' => [
                    (object)['inner_property' => 'inner_data1'],
                    (object)['inner_property' => 'inner_data2'],
                ],
            ],
            $result
        );

        $result = $coreNormalizer->normalize((new MyData())->setProperty('my_data')->setInnerData(
            (new InnerData())->setProperty('inner_data')->setOptionalProperty('optional_value')
        )->setInnerDataList([
            (new InnerData())->setProperty('inner_data1')->setOptionalProperty('optional_value1'),
            (new InnerData())->setProperty('inner_data2'),
        ]), null, new NormalizationContext(
            $coreNormalizer,
            ['inner.optional_property', '*', 'inner_list.optional_property']
        ));
        $this->assertEquals((object)[
            'property' => 'my_data',
            'inner' => (object)['inner_property' => 'inner_data', 'optional_property' => 'optional_value'],
            'inner_list' => [
                (object)['inner_property' => 'inner_data1', 'optional_property' => 'optional_value1'],
                (object)['inner_property' => 'inner_data2'],
            ],
        ], $result);

        $result = $coreNormalizer->normalize((new MyData())->setProperty('my_data'));
        $this->assertEquals((object)['property' => 'my_data', 'inner_list' => []], $result);

        $result = $coreNormalizer->normalize((new MyData()));
        $this->assertEquals((object)['inner_list' => []], $result);

        $result = $coreNormalizer->normalize([1, null]);
        $this->assertEquals([1, null], $result);

        $result = $coreNormalizer->normalize(new ArrayIterator([1, null]));
        $this->assertEquals([1, null], $result);

        $result = $coreNormalizer->normalize((new MyData())->setInnerDataList([
            (new InnerData())->setProperty('inner_data1'),
        ]), 'wrapped', new NormalizationContext(
            $coreNormalizer,
            ['inner_list']
        ));
        $this->assertEquals((object)[
            'inner_list' => [
                (object)['inner_property' => 'inner_data1'],
            ],
        ], $result);
    }

    public function testNormalizeWithDifferentGroups()
    {
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();

        $array = ['group' => 'none'];
        $normalizer = Mockery::mock(NormalizerInterface::class);
        $normalizer->shouldReceive('normalize')->andReturn($array);

        $arrayForA = ['group' => 'A'];
        $normalizerForA = Mockery::mock(NormalizerInterface::class);
        $normalizerForA->shouldReceive('normalize')->andReturn($arrayForA);

        $normalizerRegistryProvider->addNormalizer($normalizer, 'type');
        $normalizerRegistryProvider->addNormalizer($normalizerForA, 'type', 'groupA');

        $coreNormalizer = new CoreNormalizer($normalizerRegistryProvider, new TypeGuesser(), new DataFilter());
        $context = new NormalizationContext($coreNormalizer, [], 'groupA');
        $result = $coreNormalizer->normalize((object)[], 'type', $context);

        $this->assertEquals($arrayForA, (array)$result);
    }
}
