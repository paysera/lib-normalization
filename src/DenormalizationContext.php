<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class DenormalizationContext
{
    private $coreDenormalizer;
    private $normalizationGroup;

    public function __construct(CoreDenormalizer $coreDenormalizer, string $normalizationGroup = null)
    {
        $this->coreDenormalizer = $coreDenormalizer;
        $this->normalizationGroup = $normalizationGroup;
    }

    public function denormalize($data, string $type)
    {
        return $this->coreDenormalizer->denormalize($data, $type, $this);
    }

    public function denormalizeArray(array $data, string $itemType)
    {
        return $this->coreDenormalizer->denormalize($data, $itemType . '[]', $this);
    }

    /**
     * return string|null
     */
    public function getNormalizationGroup()
    {
        return $this->normalizationGroup;
    }
}
