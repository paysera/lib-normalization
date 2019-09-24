<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Registry;

use InvalidArgumentException;
use Paysera\Component\Normalization\Denormalizer\ArrayDenormalizer;
use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;
use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;
use Paysera\Component\Normalization\Normalizer\ArrayNormalizer;
use Paysera\Component\Normalization\Normalizer\PlainNormalizer;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\NormalizerRegistryInterface;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;
use Paysera\Component\Normalization\TypeAwareInterface;

/**
 * @internal
 */
class SimpleNormalizerRegistry implements NormalizerRegistryInterface
{
    /**
     * @var NormalizerInterface[]
     */
    private $normalizers;

    /**
     * @var ObjectDenormalizerInterface[]
     */
    private $objectDenormalizers;

    /**
     * @var MixedTypeDenormalizerInterface[]
     */
    private $mixedTypeDenormalizers;

    public function __construct()
    {
        $this->normalizers = [];
        $this->objectDenormalizers = [];
        $this->mixedTypeDenormalizers = [];

        $this->addNormalizer(new PlainNormalizer());
        $this->addNormalizer(new ArrayNormalizer());
        $this->addMixedTypeDenormalizer(new PlainNormalizer());
    }

    public function addTypeAwareNormalizer(TypeAwareInterface $normalizer)
    {
        if ($normalizer instanceof NormalizerInterface) {
            $this->addNormalizer($normalizer);
        }

        if ($normalizer instanceof MixedTypeDenormalizerInterface) {
            $this->addMixedTypeDenormalizer($normalizer);
        } elseif ($normalizer instanceof ObjectDenormalizerInterface) {
            $this->addObjectDenormalizer($normalizer);
        } elseif (!$normalizer instanceof NormalizerInterface) {
            throw new InvalidArgumentException(sprintf(
                'Given object of class "%s" must be either Normalizer or Denormalizer',
                get_class($normalizer)
            ));
        }
    }

    public function addNormalizer(NormalizerInterface $normalizer, string $type = null)
    {
        $type = $type ?? $this->resolveType($normalizer);
        if (isset($this->normalizers[$type])) {
            throw new InvalidArgumentException(
                sprintf('Registering duplicate normalizer for same "%s" type', $type)
            );
        }
        $this->normalizers[$type] = $normalizer;
    }

    public function addObjectDenormalizer(ObjectDenormalizerInterface $denormalizer, string $type = null)
    {
        $type = $type ?? $this->resolveType($denormalizer);
        if (isset($this->objectDenormalizers[$type]) || isset($this->mixedTypeDenormalizers[$type])) {
            throw new InvalidArgumentException(
                sprintf('Registering duplicate denormalizer for same "%s" type', $type)
            );
        }
        $this->objectDenormalizers[$type] = $denormalizer;
    }

    public function addMixedTypeDenormalizer(MixedTypeDenormalizerInterface $denormalizer, string $type = null)
    {
        $type = $type ?? $this->resolveType($denormalizer);
        if (isset($this->objectDenormalizers[$type]) || isset($this->mixedTypeDenormalizers[$type])) {
            throw new InvalidArgumentException(
                sprintf('Registering duplicate denormalizer for same "%s" type', $type)
            );
        }
        $this->mixedTypeDenormalizers[$type] = $denormalizer;
    }

    private function resolveType($normalizer)
    {
        if ($normalizer instanceof TypeAwareInterface) {
            return $normalizer->getType();
        }

        throw new InvalidArgumentException('Missing type when registering normalizer which is not type-aware');
    }

    public function getNormalizer(string $type): NormalizerInterface
    {
        if (!isset($this->normalizers[$type])) {
            throw new NormalizerNotFoundException($type);
        }
        return $this->normalizers[$type];
    }

    public function hasNormalizer(string $type): bool
    {
        return isset($this->normalizers[$type]);
    }

    public function getObjectDenormalizer(string $type): ObjectDenormalizerInterface
    {
        if (!isset($this->objectDenormalizers[$type])) {
            throw new NormalizerNotFoundException($type);
        }
        return $this->objectDenormalizers[$type];
    }

    public function getMixedTypeDenormalizer(string $type): MixedTypeDenormalizerInterface
    {
        if (!isset($this->mixedTypeDenormalizers[$type])) {
            if (!isset($this->objectDenormalizers[$type]) && substr($type, -2) === '[]') {
                return new ArrayDenormalizer(substr($type, 0, -2));
            }
            throw new NormalizerNotFoundException($type);
        }
        return $this->mixedTypeDenormalizers[$type];
    }

    public function getDenormalizerType(string $type): string
    {
        if (isset($this->objectDenormalizers[$type])) {
            return self::DENORMALIZER_TYPE_OBJECT;
        }
        if (isset($this->mixedTypeDenormalizers[$type]) || substr($type, -2) === '[]') {
            return self::DENORMALIZER_TYPE_MIXED;
        }
        return self::DENORMALIZER_TYPE_NONE;
    }
}
