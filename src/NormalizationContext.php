<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class NormalizationContext
{
    private $coreNormalizer;
    private $defaultFieldsIncluded;
    private $includedFields;
    private $path;
    private $normalizationGroup;

    public function __construct(
        CoreNormalizer $coreNormalizer,
        array $includedFields = [],
        string $normalizationGroup = null
    ) {
        $this->coreNormalizer = $coreNormalizer;
        $this->setIncludedFields($includedFields);
        $this->normalizationGroup = $normalizationGroup;
        $this->path = [];
    }

    private function setIncludedFields(array $includedFields, bool $defaultFieldsIncluded = false)
    {
        $this->defaultFieldsIncluded = (
            $defaultFieldsIncluded
            || count($includedFields) === 0
            || in_array('*', $includedFields, true)
        );
        $this->includedFields = $includedFields;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getNormalizationGroup()
    {
        return $this->normalizationGroup;
    }

    public function normalize($data, string $fieldName, string $type = null)
    {
        if (!$this->isFieldIncluded($fieldName) && !$this->isArrayItem($fieldName)) {
            return null;
        }

        return $this->coreNormalizer->normalize($data, $type, $this->createScopedContext($fieldName));
    }

    public function createScopedContext(string $fieldName): self
    {
        $context = clone $this;

        if ($this->isArrayItem($fieldName)) {
            return $context;
        }

        $includedFields = [];
        $keyPrefix = $fieldName . '.';
        $keyLength = strlen($keyPrefix);
        foreach ($this->includedFields as $fieldPattern) {
            if (substr($fieldPattern, 0, $keyLength) === $keyPrefix) {
                $includedFields[] = substr($fieldPattern, $keyLength);
            }
        }

        $context->setIncludedFields($includedFields, $this->defaultFieldsIncluded);
        $context->path[] = $fieldName;

        return $context;
    }

    public function isFieldExplicitlyIncluded(string $fieldName): bool
    {
        return in_array($fieldName, $this->includedFields, true);
    }

    public function isFieldIncluded(string $fieldName): bool
    {
        return $this->defaultFieldsIncluded || in_array($fieldName, $this->includedFields, true);
    }

    private function isArrayItem(string $fieldName)
    {
        return $fieldName === '';
    }
}
