<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization;

use Paysera\Component\Normalization\Exception\NormalizerNotFoundException;

interface TypeGuesserInterface
{
    /**
     * @param mixed $data
     * @param NormalizerRegistry $registry
     * @return string
     *
     * @throws NormalizerNotFoundException
     */
    public function guessType($data, NormalizerRegistry $registry) : string;
}
