# Normalization library

[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-packagist]
[![Latest Stable Version][ico-version]][link-packagist]
[![PHP Version Require][ico-php]][link-packagist]
[![License][ico-license]](LICENSE)

This library allows to de/normalize your business entities (plain PHP objects)
without tightly coupling them with your normalization format. You would usually do this
before converting normalized structure to JSON or after converting from it.

If you intend to use the library with Symfony, use 
[lib-normalization-bundle](https://github.com/paysera/lib-normalization-bundle) instead.

## Why?

Symfony has Serializer component that has normalizers as a part of it.
This component is created for similar reasons but with different approach.

Symfony component exposes your business entities by default, but allows sophisticated but
challenging configuration options. It also writing custom normalization logic, but it usually
resides inside your normalized classes (which probably are plain PHP objects).

Paysera Normalization library embraces simplicity by always writing a bit of code
for getting full control of the situation – normalization logic is placed in related classes,
which are usually registered from DIC.
This allows to use other services, fetch data from database, call remote services if needed or
make any other things in familiar PHP source code. You can easily rename any fields, use any custom
naming, duplicate some data for backward compatibility or, well, just write any other code.
No difficult configuration is needed for edge-cases, as you have full control over the situation.

Main features of this library:
- supports explicit type safety when denormalizing by integrating 
[lib-object-wrapper](https://github.com/paysera/lib-object-wrapper);
- normalization type can be guessed by passed data;
- easily reuse other de/normalizers without direct dependencies;
- supports different normalization groups with fallback to default one;
- supports explicitly or implicitly included fields, allowing performance tuning in normalization process.

## Installation

```bash
composer require paysera/lib-normalization
```

## Usage

### Basic usage

Write de/normalizers for your business entities:

```php
<?php

// ...

class ContactDetailsNormalizer implements NormalizerInterface, ObjectDenormalizerInterface, TypeAwareInterface
{
    public function getType(): string
    {
        return ContactDetails::class;
    }

    /**
     * @param ContactDetails $data
     * @param NormalizationContext $normalizationContext
     *
     * @return array
     */
    public function normalize($data, NormalizationContext $normalizationContext)
    {
        return [
            'email' => $data->getEmail(),
            // will automatically follow-up with normalization by guessed types:
            'residence_address' => $data->getResidenceAddress(),
            'shipping_addesses' => $data->getShippingAddresses(),
        ];
    }

    public function denormalize(ObjectWrapper $data, DenormalizationContext $context)
    {
        return (new ContactDetails())
            ->setEmail($data->getRequiredString('email'))
            ->setResidenceAddress(
                $context->denormalize($data->getRequiredObject('residence_address'), Address::class)
            )
            ->setShippingAddresses(
                $context->denormalizeArray($data->getArrayOfObject('shipping_addesses'), Address::class)
            )
        ;
    }
}
```

```php
<?php

// ...

class AddressNormalizer implements NormalizerInterface, ObjectDenormalizerInterface, TypeAwareInterface
{
    private $countryRepository;
    private $addressBuilder;

    // ...

    public function getType(): string
    {
        return Address::class;
    }

    /**
     * @param Address $data
     * @param NormalizationContext $normalizationContext
     *
     * @return array
     */
    public function normalize($data, NormalizationContext $normalizationContext)
    {
        return [
            'country_code' => $data->getCountry()->getCode(),
            'city' => $data->getCity(),
            'full_address' => $this->addressBuilder->buildAsText($data->getStreetData()),
        ];
    }

    public function denormalize(ObjectWrapper $data, DenormalizationContext $context)
    {
        $code = $data->getRequiredString('country_code');
        $country = $this->countryRepository->findOneByCode($code);
        if ($country === null) {
            throw new InvalidDataException(sprintf('Unknown country %s', $code));
        }   

        return (new Address())
            ->setCountry($country)
            ->setCity($data->getRequiredString('city'))
            ->setStreetData(
                $this->addressBuilder->parseFromText($data->getRequiredString('full_address'))
            )
        ;
    }
}
```

Register all de/normalizers in the provider:

```php
<?php

$provider = new GroupedNormalizerRegistryProvider();
$provider->addTypeAwareNormalizer(new ContactDetailsNormalizer());
$provider->addTypeAwareNormalizer(new AddressNormalizer(/* ... */));
```

Create needed services:

```php

$coreDenormalizer = new CoreDenormalizer($provider);
$coreNormalizer = new CoreNormalizer($provider, new TypeGuesser(), new DataFilter());
```

Use for de/normalization:

```php
// must be stdClass, not array
$data = json_decode('{
    "email":"a@example.com",
    "residence_address":{"country_code":"LT","city":"Vilnius","full_address":"Park street 182b-12"},
    "shipping_addresses":[]
}');
$contactDetails = $coreDenormalizer->denormalize($data, ContactDetails::class);

$normalized = $coreNormalizer->normalize($contactDetails);

var_dump($normalized);
// object(stdClass)#1 (3) { ...
```

For advanced usage please also see
[lib-normalization-bundle](https://github.com/paysera/lib-normalization-bundle) and the source code.

If you have any questions, feel free to create an issue.

## Semantic versioning

This library follows [semantic versioning](http://semver.org/spec/v2.0.0.html).

See [Symfony BC rules](http://symfony.com/doc/current/contributing/code/bc.html) for basic
information about what can be changed and what not in the API.

## Running tests

```
composer update
composer test
```

## Contributing

Feel free to create issues and give pull requests.

You can fix any code style issues using this command:
```
composer fix-cs
```

[ico-build]: https://github.com/paysera/lib-normalization/workflows/CI/badge.svg
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/paysera/lib-normalization.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/paysera/lib-normalization.svg
[ico-downloads]: https://img.shields.io/packagist/dt/paysera/lib-normalization.svg
[ico-version]: https://img.shields.io/packagist/v/paysera/lib-normalization.svg
[ico-php]: https://img.shields.io/packagist/dependency-v/paysera/lib-normalization/php
[ico-license]: https://img.shields.io/github/license/paysera/lib-normalization?color=blue

[link-build]: https://github.com/paysera/lib-normalization/actions
[link-scrutinizer]: https://scrutinizer-ci.com/g/paysera/lib-normalization/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/paysera/lib-normalization
[link-packagist]: https://packagist.org/packages/paysera/lib-normalization
