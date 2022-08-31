<?php

declare(strict_types=1);

namespace App\Model;

class Order extends AbstractBaseModel
{
    /**
     * @var string
     */
    private string $deviceType;

    /**
     * @var string
     */
    private string $deviceManufacturer;

    /**
     * @var Note|null
     */
    private ?Note $note;

    /**
     * @var string
     */
    private string $deviceBrand;

    /**
     * @return string
     */
    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    /**
     * @param string $deviceType
     */
    public function setDeviceType(string $deviceType): void
    {
        $this->deviceType = $deviceType;
    }

    /**
     * @return string
     */
    public function getDeviceManufacturer(): string
    {
        return $this->deviceManufacturer;
    }

    /**
     * @param string $deviceManufacturer
     */
    public function setDeviceManufacturer(string $deviceManufacturer): void
    {
        $this->deviceManufacturer = $deviceManufacturer;
    }

    /**
     * @return string
     */
    public function getDeviceBrand(): string
    {
        return $this->deviceBrand;
    }

    /**
     * @param string $deviceBrand
     */
    public function setDeviceBrand(string $deviceBrand): void
    {
        $this->deviceBrand = $deviceBrand;
    }

    /**
     * @return Note|null
     */
    public function getNote(): ?Note
    {
        return $this->note ?? null;
    }

    /**
     * @param Note $note
     */
    public function setNote(Note $note): void
    {
        $this->note = $note;
    }
}
