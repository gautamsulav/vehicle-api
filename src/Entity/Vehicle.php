<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=VehicleRepository::class)
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("DateTime")
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;

    /**
     * @Assert\Choice(
     * choices = {"used", "new"},
     * message = "Choose a valid type.")
     * @ORM\Column(type="string", columnDefinition="ENUM('used', 'new')")
     */
    private $type;

    /**
     * @Assert\Positive
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $msrp;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $make;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    /**
     * @Assert\PositiveOrZero
     * @ORM\Column(type="integer")
     */
    private $miles;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $vin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMsrp(): ?string
    {
        return $this->msrp;
    }

    public function setMsrp(string $msrp): self
    {
        $this->msrp = $msrp;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getMiles(): ?int
    {
        return $this->miles;
    }

    public function setMiles(int $miles): self
    {
        $this->miles = $miles;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'dateAdded' => $this->getDateAdded(),
            'type' => $this->getType(),
            'make' => $this->getMake(),
            'model' => $this->getModel(),
            'miles' => $this->getMiles(),
            'vin' => $this->getVin(),
        ];
    }


}
