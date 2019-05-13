<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContractRepository")
 */
class Contract
{

    public const MONTHLY = 'RRULE:FREQ=MONTHLY';
    public const QUARTERLY = 'RRULE:FREQ=MONTHLY;INTERVAL=3';
    public const BIANNUALLY = 'RRULE:FREQ=MONTHLY;INTERVAL=5';
    public const ANNUALLY = 'RRULE:FREQ=YEARLY';
    public const ALLOWED_INTERVALS = [self::MONTHLY, self::QUARTERLY, self::BIANNUALLY, self::ANNUALLY];

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dueInterval;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="contracts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getIntervalRule(): string
    {
        return $this->dueInterval;
    }

    public function getDueInterval(): string
    {

        switch ($this->dueInterval) {
            case self::MONTHLY:
                $interval = 'monthly';
                break;
            case self::QUARTERLY:
                $interval = 'quarterly';
                break;
            case self::BIANNUALLY:
                $interval = 'biannually';
                break;
            case self::ANNUALLY:
                $interval = 'annually';
                break;
            default:
                $interval = $this->dueInterval;
        }

        return $interval;
    }

    public function setDueInterval(string $dueInterval): self
    {
        if (!in_array($dueInterval, self::ALLOWED_INTERVALS, true)) {
            throw new \DomainException('Invalid Interval given!');
        }
        $this->dueInterval = $dueInterval;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
