<?php
declare(strict_types=1);

namespace Paysera\Component\Normalization\Tests\Fixtures\Entity;

class MyData
{
    /**
     * @var InnerData
     */
    private $innerData;

    /**
     * @var InnerData[]
     */
    private $innerDataList = [];

    /**
     * @var string
     */
    private $property;

    /**
     * @return InnerData|null
     */
    public function getInnerData()
    {
        return $this->innerData;
    }

    /**
     * @param InnerData $innerData
     * @return $this
     */
    public function setInnerData(InnerData $innerData): MyData
    {
        $this->innerData = $innerData;
        return $this;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     */
    public function setProperty(string $property): MyData
    {
        $this->property = $property;
        return $this;
    }

    /**
     * @return InnerData[]
     */
    public function getInnerDataList(): array
    {
        return $this->innerDataList;
    }

    /**
     * @param InnerData[] $innerDataList
     * @return $this
     */
    public function setInnerDataList(array $innerDataList): MyData
    {
        $this->innerDataList = $innerDataList;
        return $this;
    }

    public function addInnerData(InnerData $innerData): MyData
    {
        $this->innerDataList[] = $innerData;
        return $this;
    }
}
