<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\DeviceTypeEnum;
use App\Enum\StatusEnum;
use DateTime;

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
    private ?Note $note = null;

    /**
     * @var string
     */
    private string $deviceBrand;

    /**
     * @var string|null
     */
    private ?string $technicianName = null;

    /**
     * @var DateTime
     */
    private DateTime $created;

    /**
     * @var int
     */
    private int $status;

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
        return $this->note;
    }

    /**
     * @param Note $note
     */
    public function setNote(Note $note): void
    {
        $this->note = $note;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getTechnicianName(): ?string
    {
        return $this->technicianName;
    }

    /**
     * @param string|null $technicianName
     */
    public function setTechnicianName(?string $technicianName): void
    {
        $this->technicianName = $technicianName;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $errors = [];

        if (!is_string($this->getDeviceManufacturer()) || empty($this->getDeviceManufacturer())) {
            $errors[] = 'Device manufacturer is empty or not a string';
        }

        if (!is_string($this->getDeviceBrand()) || empty($this->getDeviceBrand())) {
            $errors[] = 'Device brand is empty or not a string';
        }

        if (
            !is_string($this->getDeviceType())
            || empty($this->getDeviceType())
            || !in_array($this->getDeviceType(), DeviceTypeEnum::TYPES)
        ) {
            $deviceTypes = implode(", ", DeviceTypeEnum::TYPES);
            $errors[] = "Device type is empty, not a string or not a valid type ({$deviceTypes})";
        }

        $this->setErrors($errors);

        return count($this->getErrors()) === 0;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'deviceType' => $this->getDeviceType(),
            'deviceManufacturer' => $this->getDeviceManufacturer(),
            'deviceBrand' => $this->getDeviceBrand(),
            'technician' => $this->getTechnicianName(),
            'status' => StatusEnum::STATUSES[$this->getStatus()],
            'created' => $this->getCreated()->format('Y-m-d H:i:s')
        ];
    }
}
