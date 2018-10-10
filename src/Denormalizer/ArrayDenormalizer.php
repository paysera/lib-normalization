<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Denormalizer;

use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\Exception\InvalidDataException;
use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;

class ArrayDenormalizer implements MixedTypeDenormalizerInterface
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $data
     * @param DenormalizationContext $context
     * @return mixed
     */
    public function denormalize($data, DenormalizationContext $context)
    {
        if (!is_array($data)) {
            throw new InvalidDataException(sprintf('Expected array, got %s', gettype($data)));
        }

        foreach ($data as &$item) {
            $item = $context->denormalize($item, $this->type);
        }

        return $data;
    }
}
