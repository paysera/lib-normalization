<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

class DataFilter
{
    public function filterData($data, NormalizationContext $context)
    {
        if (is_array($data)) {
            if ($this->isAssociativeArray($data)) {
                return $this->filterObject($data, $context);
            }
            return $this->filterArray($data, $context);
        } elseif ($data instanceof \stdClass) {
            return $this->filterObject($data, $context);
        } elseif (is_scalar($data)) {
            return $data;
        }

        return $context->normalize($data, '');
    }

    private function isAssociativeArray(array $array)
    {
        for ($i = 0; $i < count($array); $i++) {
            if (!array_key_exists($i, $array)) {
                return true;
            }
        }
        return false;
    }

    private function filterObject($data, NormalizationContext $context)
    {
        $result = new \stdClass();
        foreach ($data as $key => $value) {
            if ($value !== null && $context->isFieldIncluded($key)) {
                $result->$key = $this->filterData($value, $context->createScopedContext($key));
            }
        }
        return $result;
    }

    private function filterArray($data, NormalizationContext $context)
    {
        foreach ($data as &$value) {
            $value = $this->filterData($value, $context);
        }
        return $data;
    }
}
