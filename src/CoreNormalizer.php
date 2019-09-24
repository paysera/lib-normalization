<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class CoreNormalizer
{
    private $registryProvider;
    private $typeGuesser;
    private $dataFilter;

    public function __construct(
        NormalizerRegistryProviderInterface $registryProvider,
        TypeGuesserInterface $typeGuesser,
        DataFilter $dataFilter
    ) {
        $this->registryProvider = $registryProvider;
        $this->typeGuesser = $typeGuesser;
        $this->dataFilter = $dataFilter;
    }

    public function normalize($data, string $type = null, NormalizationContext $context = null)
    {
        if ($data === null) {
            return null;
        }

        if ($context === null) {
            $context = new NormalizationContext($this);
        }
        $registry = $this->registryProvider->getNormalizerRegistryForNormalizationGroup(
            $context->getNormalizationGroup()
        );

        if ($type === null) {
            $type = $this->typeGuesser->guessType($data, $registry);
        }
        $normalizer = $registry->getNormalizer($type);

        $result = $normalizer->normalize($data, $context);

        return $this->dataFilter->filterData($result, $context);
    }
}
