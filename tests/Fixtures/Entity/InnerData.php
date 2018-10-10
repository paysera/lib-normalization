<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Entity;

class InnerData
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $optionalProperty;

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     */
    public function setProperty(string $property): InnerData
    {
        $this->property = $property;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOptionalProperty()
    {
        return $this->optionalProperty;
    }

    /**
     * @param string $optionalProperty
     * @return $this
     */
    public function setOptionalProperty(string $optionalProperty): InnerData
    {
        $this->optionalProperty = $optionalProperty;
        return $this;
    }
}
