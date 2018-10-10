<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Paysera\Component\Normalization\Exception\InvalidDataException;

interface MixedTypeDenormalizerInterface
{

    /**
     * Converts arbitrary input data to any data that is used by your business logic.
     * If input data was object, instance of ObjectWrapper will be passed as an argument.
     * Always validate the type of input data here as it could be anything.
     * Most of the cases returned object should be instance of class,
     * which equals to the type when registering denormalizer.
     *
     * @param mixed $input
     * @param DenormalizationContext $context
     * @return mixed
     *
     * @throws InvalidDataException
     */
    public function denormalize($input, DenormalizationContext $context);
}
