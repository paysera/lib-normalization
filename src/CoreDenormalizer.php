<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use InvalidArgumentException;
use stdClass;
use Paysera\Component\Normalization\Exception\InvalidDataException;
use Paysera\Component\ObjectWrapper\ObjectWrapper;

class CoreDenormalizer
{
    protected $registryProvider;

    public function __construct(NormalizerRegistryProviderInterface $registryProvider)
    {
        $this->registryProvider = $registryProvider;
    }

    public function denormalize($data, string $type, DenormalizationContext $context = null)
    {
        if ($data === null) {
            return null;
        }

        $normalizationGroup = $context !== null ? $context->getNormalizationGroup() : null;
        $registry = $this->registryProvider->getNormalizerRegistryForNormalizationGroup($normalizationGroup);

        $denormalizerType = $registry->getDenormalizerType($type);

        if ($denormalizerType === NormalizerRegistryInterface::DENORMALIZER_TYPE_MIXED) {
            $denormalizer = $registry->getMixedTypeDenormalizer($type);
        } elseif ($denormalizerType === NormalizerRegistryInterface::DENORMALIZER_TYPE_OBJECT) {
            if (!$data instanceof stdClass && !$data instanceof ObjectWrapper) {
                throw new InvalidDataException(sprintf('Expected object, got %s', gettype($data)));
            }
            $denormalizer = $registry->getObjectDenormalizer($type);
        } else {
            throw new InvalidArgumentException(sprintf('Denormalizer with type "%s" is not registered', $type));
        }

        if ($data instanceof stdClass) {
            $data = new ObjectWrapper($data);
        }

        if ($context === null) {
            $context = new DenormalizationContext($this);
        }

        return $denormalizer->denormalize($data, $context);
    }
}
