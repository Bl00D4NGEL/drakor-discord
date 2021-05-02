<?php

namespace App\Entity;

use App\Repository\LogEntryRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogEntryRepository::class)
 */
class LogEntry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $creationTime;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $rawResult;

    public function __construct()
    {
        $this->creationTime = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationTime(): ?DateTimeInterface
    {
        return $this->creationTime;
    }

    public function setCreationTime(DateTimeInterface $creationTime): self
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    public function getRawResult(): ?string
    {
        return $this->rawResult;
    }

    public function setRawResult(string $rawResult): self
    {
        $this->rawResult = $rawResult;

        return $this;
    }
}
