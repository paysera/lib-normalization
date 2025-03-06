<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Paysera\Component\Normalization\CoreDenormalizer;
use Paysera\Component\Normalization\CoreNormalizer;
use Paysera\Component\Normalization\DataFilter;
use Paysera\Component\Normalization\Normalizer\DateTimeImmutableNormalizer;
use Paysera\Component\Normalization\Normalizer\DateTimeNormalizer;
use Paysera\Component\Normalization\Registry\GroupedNormalizerRegistryProvider;
use Paysera\Component\Normalization\TypeGuesser;

class DateTimeNormalizerFunctionalTest extends MockeryTestCase
{
    const TIMESTAMP_FORMAT = 'U';
    const DATETIME_FORMAT = 'Y-m-d\TH:i:sP';

    public function testDateTimeNormalizerWithTimestampFormat()
    {
        $normalizerRegistryProvider = $this->getGroupedNormalizerRegistryProvider(self::TIMESTAMP_FORMAT);

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistryProvider);
        $coreNormalizer = new CoreNormalizer($normalizerRegistryProvider, new TypeGuesser(), new DataFilter());

        $timestamp = 1514808794;

        $dateTime = new DateTime('now', new DateTimeZone('Europe/Vilnius'));
        $dateTime->setTimestamp($timestamp);
        $normalized = $coreNormalizer->normalize($dateTime);

        $this->assertSame((string)$timestamp, $normalized);

        /** @var DateTimeImmutable $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTimeImmutable::class);
        $this->assertInstanceOf(DateTimeImmutable::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());

        /** @var DateTimeInterface $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTimeInterface::class);
        $this->assertInstanceOf(DateTimeImmutable::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());

        /** @var DateTime $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTime::class);
        $this->assertInstanceOf(DateTime::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());
    }

    public function testDateTimeNormalizer()
    {
        $normalizerRegistryProvider = $this->getGroupedNormalizerRegistryProvider(self::DATETIME_FORMAT);

        $coreDenormalizer = new CoreDenormalizer($normalizerRegistryProvider);
        $coreNormalizer = new CoreNormalizer($normalizerRegistryProvider, new TypeGuesser(), new DataFilter());

        $timestamp = 1514808794;

        $dateTime = new DateTime('now', new DateTimeZone('Europe/Vilnius'));
        $dateTime->setTimestamp($timestamp);
        $expected = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
        $expected->setTimestamp($timestamp);

        $normalized = $coreNormalizer->normalize($dateTime);

        $this->assertSame($expected->format('Y-m-d\TH:i:sP'), $normalized);

        /** @var DateTimeImmutable $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTimeImmutable::class);
        $this->assertInstanceOf(DateTimeImmutable::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());

        /** @var DateTimeInterface $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTimeInterface::class);
        $this->assertInstanceOf(DateTimeImmutable::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());

        /** @var DateTime $denormalized */
        $denormalized = $coreDenormalizer->denormalize($normalized, DateTime::class);
        $this->assertInstanceOf(DateTime::class, $denormalized);
        $this->assertSame($timestamp, $denormalized->getTimestamp());
    }

    private function getGroupedNormalizerRegistryProvider(string $format)
    {
        $normalizerRegistryProvider = new GroupedNormalizerRegistryProvider();

        $normalizerRegistryProvider->addNormalizer(new DateTimeNormalizer($format));
        $normalizerRegistryProvider->addNormalizer(new DateTimeImmutableNormalizer($format));

        $normalizerRegistryProvider->addMixedTypeDenormalizer(new DateTimeNormalizer($format));
        $normalizerRegistryProvider->addMixedTypeDenormalizer(new DateTimeImmutableNormalizer($format));
        $normalizerRegistryProvider->addMixedTypeDenormalizer(
            new DateTimeImmutableNormalizer($format),
            DateTimeInterface::class
        );

        return $normalizerRegistryProvider;
    }
}
