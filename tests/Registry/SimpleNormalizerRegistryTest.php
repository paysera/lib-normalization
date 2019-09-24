<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;
use Paysera\Component\Normalization\Normalizer\PlainNormalizer;
use Paysera\Component\Normalization\Registry\SimpleNormalizerRegistry;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\MyDataDenormalizer;

class SimpleNormalizerRegistryTest extends MockeryTestCase
{
    public function testReturnsOverriddenArrayDenormalizer()
    {
        $registry = new SimpleNormalizerRegistry();
        $denormalizer = new PlainNormalizer();
        $registry->addMixedTypeDenormalizer($denormalizer, 'stdClass[]');

        $this->assertSame($denormalizer, $registry->getMixedTypeDenormalizer('stdClass[]'));
    }

    public function testDoesNotCreateArrayDenormalizerIfRegisteredAsObjectDenormalizer()
    {
        $registry = new SimpleNormalizerRegistry();
        $registry->addObjectDenormalizer(new MyDataDenormalizer(), 'stdClass[]');

        $this->expectException(NormalizerNotFoundException::class);
        $registry->getMixedTypeDenormalizer('stdClass[]');
    }
}
