<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Paysera\Component\Normalization\Exception\InvalidDataException;
use Paysera\Component\ObjectWrapper\ObjectWrapper;

interface ObjectDenormalizerInterface
{

    /**
     * Converts input data (which is always object in this case) to any data that is used by your business logic.
     * Return value is usually plain object (POPO / ValueObject) or Doctrine Entity.
     * Most of the cases returned object should be instance of class,
     * which equals to the type when registering denormalizer.
     *
     * @param ObjectWrapper $input
     * @param DenormalizationContext $context
     * @return mixed
     *
     * @throws InvalidDataException
     */
    public function denormalize(ObjectWrapper $input, DenormalizationContext $context);
}
