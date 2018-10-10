<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Paysera\Component\Normalization\CoreNormalizer;
use Paysera\Component\Normalization\DataFilter;
use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerRegistry;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\InnerData;
use Paysera\Component\Normalization\Tests\Fixtures\Entity\MyData;
use Paysera\Component\Normalization\Tests\Fixtures\Normalizer\InnerDataNormalizer;
use Paysera\Component\Normalization\Tests\Fixtures\Normalizer\MyDataNormalizer;
use Paysera\Component\Normalization\TypeGuesser;
use PHPUnit\Framework\TestCase;

class CoreNormalizerFunctionalTest extends TestCase
{

    public function testNormalize()
    {
        $normalizerRegistry = new NormalizerRegistry();
        $normalizerRegistry->addNormalizer(new MyDataNormalizer());
        $normalizerRegistry->addNormalizer(new InnerDataNormalizer());

        $typeGuesser = new TypeGuesser();
        $dataFilter = new DataFilter();

        $coreNormalizer = new CoreNormalizer($normalizerRegistry, $typeGuesser, $dataFilter);

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

        $result = $coreNormalizer->normalize(new \ArrayIterator([1, null]));
        $this->assertEquals([1, null], $result);
    }
}
