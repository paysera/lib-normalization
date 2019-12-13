<?php

declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Paysera\Component\Normalization\CoreNormalizer;
use Paysera\Component\Normalization\DataFilter;
use Paysera\Component\Normalization\NormalizationContext;
use stdClass;

class DataFilterTest extends MockeryTestCase
{
    /**
     * @dataProvider providerForFilterDataWithoutFiltering
     * @param string $expected
     * @param mixed $input
     */
    public function testFilterDataWithoutFiltering(string $expected, $input)
    {
        $normalizer = Mockery::mock(CoreNormalizer::class);
        $context = new NormalizationContext($normalizer);

        $dataFilter = new DataFilter();

        $output = $dataFilter->filterData($input, $context);
        $this->assertEquals($expected, json_encode($output));
    }

    public function providerForFilterDataWithoutFiltering()
    {
        return [
            ['{}', new stdClass()],
            ['[]', []],
            ['{"a":"b"}', ['a' => 'b']],
            ['{"a":"b","1":2}', ['a' => 'b', 1 => 2]],
            ['[1,2]', [0 => 1, 1 => 2]],
            ['{"0":1,"2":2}', [0 => 1, 2 => 2]],
            ['{"0":null,"2":2}', [0 => null, 2 => 2]],
        ];
    }
}
