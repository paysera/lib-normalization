<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

interface NormalizerInterface
{
    /**
     * Converts data from your business logic (usually Doctrine Entities or other objects) into JSON-encodable
     * output data (associative arrays or other basic structures or types).
     *
     * @param mixed $entity Usually an object from your business logic
     * @param NormalizationContext $normalizationContext
     *
     * @return array|string|int|float|bool|\stdClass|null output data representing given business entity
     */
    public function normalize($entity, NormalizationContext $normalizationContext);
}
