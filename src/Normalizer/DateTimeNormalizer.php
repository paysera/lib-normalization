<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Normalizer;

use DateTime;
use DateTimeZone;
use Paysera\Component\Normalization\DenormalizationContext;
use Paysera\Component\Normalization\Exception\InvalidDataException;
use Paysera\Component\Normalization\MixedTypeDenormalizerInterface;
use Paysera\Component\Normalization\NormalizationContext;
use Paysera\Component\Normalization\NormalizerInterface;
use Paysera\Component\Normalization\TypeAwareInterface;

class DateTimeNormalizer implements NormalizerInterface, MixedTypeDenormalizerInterface, TypeAwareInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var DateTimeZone
     */
    protected $remoteTimezone;

    public function __construct($format, $remoteTimezone = null)
    {
        $this->format = $format;
        $this->remoteTimezone = $remoteTimezone !== null ? $remoteTimezone : $this->getLocalTimezone();
    }

    /**
     * @param \DateTime|\DateTimeImmutable $entity
     * @param NormalizationContext $normalizationContext
     * @return string
     */
    public function normalize($entity, NormalizationContext $normalizationContext)
    {
        $entity = clone $entity;
        $entity = $entity->setTimezone($this->remoteTimezone);

        return $entity->format($this->format);
    }

    public function denormalize($input, DenormalizationContext $context)
    {
        if (!is_scalar($input)) {
            throw new InvalidDataException('Expected scalar type to be passed as a date');
        }

        $date = $this->createDateTimeFromFormat((string)$input);
        if ($date === false) {
            throw new InvalidDataException('Provided date format is invalid');
        }
        $dateErrors = date_get_last_errors();
        if ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0) {
            throw new InvalidDataException('The parsed date was invalid');
        }
        return $date->setTimezone($this->getLocalTimezone());
    }

    protected function getLocalTimezone(): DateTimeZone
    {
        return new DateTimeZone(date_default_timezone_get());
    }

    public function getType(): string
    {
        return DateTime::class;
    }

    protected function createDateTimeFromFormat(string $input)
    {
        return DateTime::createFromFormat(
            $this->format,
            $input,
            $this->remoteTimezone
        );
    }
}
