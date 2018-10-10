<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Normalizer;

use DateTimeImmutable;

class DateTimeImmutableNormalizer extends DateTimeNormalizer
{
    public function getType(): string
    {
        return DateTimeImmutable::class;
    }

    protected function createDateTimeFromFormat(string $input)
    {
        return DateTimeImmutable::createFromFormat(
            $this->format,
            $input,
            $this->remoteTimezone
        );
    }
}
