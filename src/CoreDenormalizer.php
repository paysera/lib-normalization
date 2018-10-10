<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Paysera\Component\Normalization\Exception\InvalidDataException;
use Paysera\Component\ObjectWrapper\ObjectWrapper;

class CoreDenormalizer
{
    protected $registry;

    public function __construct(NormalizerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function denormalize($data, string $type)
    {
        if ($data === null) {
            return null;
        }

        $denormalizerType = $this->registry->getDenormalizerType($type);

        if ($denormalizerType === NormalizerRegistry::DENORMALIZER_TYPE_MIXED) {
            $denormalizer = $this->registry->getMixedTypeDenormalizer($type);
        } elseif ($denormalizerType === NormalizerRegistry::DENORMALIZER_TYPE_OBJECT) {
            if (!$data instanceof \stdClass && !$data instanceof ObjectWrapper) {
                throw new InvalidDataException(sprintf('Expected object, got %s', gettype($data)));
            }
            $denormalizer = $this->registry->getObjectDenormalizer($type);
        } else {
            throw new \InvalidArgumentException(sprintf('Denormalizer with type "%s" is not registered', $type));
        }

        if ($data instanceof \stdClass) {
            $data = new ObjectWrapper($data);
        }

        return $denormalizer->denormalize($data, new DenormalizationContext($this));
    }
}
