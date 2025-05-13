<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel_number = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner le nom")]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $society = null;

    #[ORM\Column]
    private ?bool $contacted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelNumber(): ?string
    {
        return $this->tel_number;
    }

    public function setTelNumber(?string $tel_number): static
    {
        $this->tel_number = $tel_number;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSociety(): ?string
    {
        return $this->society;
    }

    public function setSociety(?string $society): static
    {
        $this->society = $society;

        return $this;
    }

    public function isContacted(): ?bool
    {
        return $this->contacted;
    }

    public function setContacted(bool $contacted): static
    {
        $this->contacted = $contacted;

        return $this;
    }
}
