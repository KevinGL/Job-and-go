<?php

namespace App\Entity;

use App\Repository\CandidacyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidacyRepository::class)]
class Candidacy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $society = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $candidacy_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $relaunch_date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $issue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSociety(): ?string
    {
        return $this->society;
    }

    public function setSociety(string $society): static
    {
        $this->society = $society;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCandidacyDate(): ?\DateTimeInterface
    {
        return $this->candidacy_date;
    }

    public function setCandidacyDate(\DateTimeInterface $candidacy_date): static
    {
        $this->candidacy_date = $candidacy_date;

        return $this;
    }

    public function getRelaunchDate(): ?\DateTimeInterface
    {
        return $this->relaunch_date;
    }

    public function setRelaunchDate(?\DateTimeInterface $relaunch_date): static
    {
        $this->relaunch_date = $relaunch_date;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getIssue(): ?string
    {
        return $this->issue;
    }

    public function setIssue(?string $issue): static
    {
        $this->issue = $issue;

        return $this;
    }

    public function __construct()
    {
        //$this->candidacy_date = new \DateTimeImmutable();
    }
}
