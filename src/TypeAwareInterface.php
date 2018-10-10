<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

/**
 * Use only with NormalizerInterface, ObjectDenormalizerInterface or MixedTypeDenormalizerInterface
 */
interface TypeAwareInterface
{
    /**
     * Returns type to be used to register this normalizer or denormalizer to the registry.
     * This is usually a fully qualified name of the class which is being handled by de/normalization.
     *
     * @return string
     */
    public function getType(): string;
}
