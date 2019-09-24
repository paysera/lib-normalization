<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Traversable;
use ReflectionClass;
use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;
use Paysera\Component\Normalization\Normalizer\ArrayNormalizer;
use Paysera\Component\Normalization\Normalizer\PlainNormalizer;

class TypeGuesser implements TypeGuesserInterface
{
    public function guessType($data, NormalizerRegistryInterface $registry): string
    {
        if ($data === null || is_scalar($data)) {
            return PlainNormalizer::KEY;
        } elseif (is_array($data)) {
            return ArrayNormalizer::KEY;
        }

        $className = get_class($data);
        if ($registry->hasNormalizer($className)) {
            return $className;
        }

        $type = $this->findRegisteredClass(new ReflectionClass($className), $registry);
        if ($type !== null) {
            return $type;
        }

        if ($data instanceof Traversable) {
            return ArrayNormalizer::KEY;
        }

        throw new NormalizerNotFoundException('Cannot guess normalizer for class ' . $className);
    }

    /**
     * @param ReflectionClass $reflection
     * @param NormalizerRegistryInterface $registry
     * @return null|string
     */
    protected function findRegisteredClass(ReflectionClass $reflection, NormalizerRegistryInterface $registry)
    {
        $interfaceNames = $reflection->getInterfaceNames();
        while ($parent = $reflection->getParentClass()) {
            $className = $parent->getName();
            if ($registry->hasNormalizer($className)) {
                return $className;
            }
            $reflection = $parent;
        }
        foreach ($interfaceNames as $className) {
            if ($registry->hasNormalizer($className)) {
                return $className;
            }
        }
        return null;
    }
}
