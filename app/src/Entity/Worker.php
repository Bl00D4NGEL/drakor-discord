<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkerRepository::class)
 */
class Worker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $nextScheduledAction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNextScheduledAction(): ?string
    {
        return $this->nextScheduledAction;
    }

    public function setNextScheduledAction(string $nextScheduledAction): self
    {
        $this->nextScheduledAction = $nextScheduledAction;

        return $this;
    }
}
