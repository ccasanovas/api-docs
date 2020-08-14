<?php
declare(strict_types=1);

namespace App\Domain\Documents;

use JsonSerializable;

class Documents implements JsonSerializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var int|null
     */
    private $idServices;

    /**
     * @var int|null
     */
    private $idOrganization;

    /**
     * @var int|null
     */
    private $idUser;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $container;

    /**
     * @var string
     */
    private $size;

    /**
     * @var string
     */
    private $originalName;

    /**
     * @var string
     */
    private $outputName;

    /**
     * @var string
     */
    private $fullPathName;

    /**
     * @param int|null  $id
     * @param int|null  $idServices
     * @param int|null  $idOrganization
     * @param int|null  $idUser
     * @param string    $country
     * @param string    $container
     * @param string    $size
     * @param string    $originalName
     * @param string    $outputName
     * @param string    $fullPathName
     */

    public function __construct(?int $id, int $idServices, int $idOrganization, int $idUser,
                                string $country, string $size, string $originalName, string $outputName,
                                string $fullPathName)
    {
        $this->id = $id;
        $this->idServices = $idServices;
        $this->idOrganization = $idOrganization;
        $this->idUser = $idUser;
        $this->country = $country;
        $this->size = $size;
        $this->originalName = $originalName;
        $this->outputName = $outputName;
        $this->fullPathName = $fullPathName;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getIdServices(): ?int
    {
        return $this->idServices;
    }

    /**
     * @return int|null
     */
    public function getIdOrganization(): ?int
    {
        return $this->idOrganization;
    }

    /**
     * @return int|null
     */
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getOutputName(): string
    {
        return $this->outputName;
    }

    /**
     * @return string
     */
    public function getFullPathName(): string
    {
        return $this->fullPathName;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'idServices' => $this->idServices,
            'idOrganization' => $this->idOrganization,
            'idUser' => $this->idUser,
            'country' => $this->country,
            'size' => $this->size,
            'originalName' => $this->originalName,
            'outputName' => $this->outputName,
            'fullPathName' => $this->fullPathName
        ];
    }


}
