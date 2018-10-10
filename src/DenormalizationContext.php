<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class DenormalizationContext
{
    private $coreDenormalizer;

    public function __construct(CoreDenormalizer $coreDenormalizer)
    {
        $this->coreDenormalizer = $coreDenormalizer;
    }

    public function denormalize($data, string $type)
    {
        return $this->coreDenormalizer->denormalize($data, $type);
    }

    public function denormalizeArray(array $data, string $itemType)
    {
        return $this->coreDenormalizer->denormalize($data, $itemType . '[]');
    }
}
