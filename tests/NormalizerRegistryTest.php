<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;
use Paysera\Component\Normalization\Normalizer\PlainNormalizer;
use Paysera\Component\Normalization\NormalizerRegistry;
use Paysera\Component\Normalization\Tests\Fixtures\Denormalizer\MyDataDenormalizer;
use PHPUnit\Framework\TestCase;

class NormalizerRegistryTest extends TestCase
{

    public function testReturnsOverriddenArrayDenormalizer()
    {
        $registry = new NormalizerRegistry();
        $denormalizer = new PlainNormalizer();
        $registry->addMixedTypeDenormalizer($denormalizer, 'stdClass[]');

        $this->assertSame($denormalizer, $registry->getMixedTypeDenormalizer('stdClass[]'));
    }

    public function testDoesNotCreateArrayDenormalizerIfRegisteredAsObjectDenormalizer()
    {
        $registry = new NormalizerRegistry();
        $registry->addObjectDenormalizer(new MyDataDenormalizer(), 'stdClass[]');

        $this->expectException(NormalizerNotFoundException::class);
        $registry->getMixedTypeDenormalizer('stdClass[]');
    }
}
