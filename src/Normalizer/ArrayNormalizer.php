<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Normalizer;

use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\TypeAwareInterface;

class ArrayNormalizer implements NormalizerInterface, TypeAwareInterface
{
    const KEY = 'array';

    public function normalize($data, NormalizationContext $normalizationContext)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Value passed to ArrayNormalizer must be an array or instance of Traversable, "%s" given',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $result = [];
        foreach ($data as $key => $item) {
            $result[$key] = $normalizationContext->normalize($item, '');
        }
        return $result;
    }

    public function getType(): string
    {
        return self::KEY;
    }
}
