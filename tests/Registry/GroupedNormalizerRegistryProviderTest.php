<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Paysera\Component\Normalization\Registry\GroupedNormalizerRegistryProvider;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;

class GroupedNormalizerRegistryProviderTest extends MockeryTestCase
{
    public function test()
    {
        $provider = new GroupedNormalizerRegistryProvider();

        $denormalizer1 = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizer2 = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizer3 = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizerA1 = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizerA2 = Mockery::mock(ObjectDenormalizerInterface::class);
        $denormalizerB1 = Mockery::mock(ObjectDenormalizerInterface::class);

        $provider->addObjectDenormalizer($denormalizer1, '1');
        $provider->addObjectDenormalizer($denormalizer2, '2');
        $provider->addObjectDenormalizer($denormalizer3, '3');
        $provider->addObjectDenormalizer($denormalizerA1, '1', 'a');
        $provider->addObjectDenormalizer($denormalizerA2, '2', 'a');
        $provider->addObjectDenormalizer($denormalizerB1, '1', 'b');

        $registry = $provider->getDefaultNormalizerRegistry();
        $registryForA = $provider->getNormalizerRegistryForNormalizationGroup('a');
        $registryForB = $provider->getNormalizerRegistryForNormalizationGroup('b');

        $this->assertSame($denormalizer1, $registry->getObjectDenormalizer('1'));
        $this->assertSame($denormalizer2, $registry->getObjectDenormalizer('2'));
        $this->assertSame($denormalizer3, $registry->getObjectDenormalizer('3'));

        $this->assertSame($denormalizerA1, $registryForA->getObjectDenormalizer('1'));
        $this->assertSame($denormalizerA2, $registryForA->getObjectDenormalizer('2'));
        $this->assertSame($denormalizer3, $registryForA->getObjectDenormalizer('3'));

        $this->assertSame($denormalizerB1, $registryForB->getObjectDenormalizer('1'));
        $this->assertSame($denormalizer2, $registryForB->getObjectDenormalizer('2'));
        $this->assertSame($denormalizer3, $registryForB->getObjectDenormalizer('3'));
    }
}
