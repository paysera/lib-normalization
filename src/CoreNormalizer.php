<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class CoreNormalizer
{
    private $registry;
    private $typeGuesser;
    private $dataFilter;

    public function __construct(
        NormalizerRegistry $registry,
        TypeGuesser $typeGuesser,
        DataFilter $dataFilter
    ) {
        $this->registry = $registry;
        $this->typeGuesser = $typeGuesser;
        $this->dataFilter = $dataFilter;
    }

    public function normalize($data, string $type = null, NormalizationContext $context = null)
    {
        if ($data === null) {
            return null;
        }

        if ($type === null) {
            $type = $this->typeGuesser->guessType($data, $this->registry);
        }
        $normalizer = $this->registry->getNormalizer($type);

        if ($context === null) {
            $context = new NormalizationContext($this);
        }

        $result = $normalizer->normalize($data, $context);

        return $this->dataFilter->filterData($result, $context);
    }
}
