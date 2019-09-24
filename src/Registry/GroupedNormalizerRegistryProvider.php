<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Registry;

use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\NormalizerRegistryInterface;
use Paysera\Component\Normalization\NormalizerRegistryProviderInterface;
use Paysera\Component\Normalization\ObjectDenormalizerInterface;
use Paysera\Component\Normalization\TypeAwareInterface;

class GroupedNormalizerRegistryProvider implements NormalizerRegistryProviderInterface
{
    /**
     * @var array|SimpleNormalizerRegistry[]
     */
    private $simpleRegistriesByGroup;
    /**
     * @var array|NormalizerRegistryInterface[]
     */
    private $registriesByGroup;
    private $defaultRegistry;

    public function __construct()
    {
        $this->defaultRegistry = new SimpleNormalizerRegistry();
        $this->registriesByGroup = [];
        $this->simpleRegistriesByGroup = [];
    }

    public function addTypeAwareNormalizer(TypeAwareInterface $normalizer, string $normalizationGroup = null)
    {
        $this->resolveRegistry($normalizationGroup)->addTypeAwareNormalizer($normalizer);
    }

    public function addNormalizer(
        NormalizerInterface $normalizer,
        string $type = null,
        string $normalizationGroup = null
    ) {
        $this->resolveRegistry($normalizationGroup)->addNormalizer($normalizer, $type);
    }

    public function addObjectDenormalizer(
        ObjectDenormalizerInterface $denormalizer,
        string $type = null,
        string $normalizationGroup = null
    ) {
        $this->resolveRegistry($normalizationGroup)->addObjectDenormalizer($denormalizer, $type);
    }

    public function addMixedTypeDenormalizer(
        MixedTypeDenormalizerInterface $denormalizer,
        string $type = null,
        string $normalizationGroup = null
    ) {
        $this->resolveRegistry($normalizationGroup)->addMixedTypeDenormalizer($denormalizer, $type);
    }

    private function resolveRegistry(string $normalizationGroup = null): SimpleNormalizerRegistry
    {
        if ($normalizationGroup === null) {
            return $this->defaultRegistry;
        }

        if (!isset($this->registriesByGroup[$normalizationGroup])) {
            $simpleRegistry = new SimpleNormalizerRegistry();
            $registry = new FallbackNormalizerRegistry($simpleRegistry, $this->defaultRegistry);

            $this->simpleRegistriesByGroup[$normalizationGroup] = $simpleRegistry;
            $this->registriesByGroup[$normalizationGroup] = $registry;
        }

        return $this->simpleRegistriesByGroup[$normalizationGroup];
    }

    public function getDefaultNormalizerRegistry(): NormalizerRegistryInterface
    {
        return $this->defaultRegistry;
    }

    public function getNormalizerRegistryForNormalizationGroup(
        string $normalizationGroup
        = null
    ): NormalizerRegistryInterface {
        if ($normalizationGroup === null || !isset($this->registriesByGroup[$normalizationGroup])) {
            return $this->defaultRegistry;
        }

        return $this->registriesByGroup[$normalizationGroup];
    }
}
