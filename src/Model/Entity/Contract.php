<?php

namespace App\Model\Entity;


class Contract
{

    public const MONTHLY = 'RRULE:FREQ=MONTHLY';
    public const QUARTERLY = 'RRULE:FREQ=MONTHLY;INTERVAL=3';
    public const BIANNUALLY = 'RRULE:FREQ=MONTHLY;INTERVAL=5';
    public const ANNUALLY = 'RRULE:FREQ=YEARLY';
    public const ALLOWED_INTERVALS = [self::MONTHLY, self::QUARTERLY, self::BIANNUALLY, self::ANNUALLY];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var \DateTimeInterface
     */
    private $startDate;

    /**
     * @var \DateTimeInterface
     */
    private $endDate;

    /**
     * @var string
     */
    private $dueInterval;

    /**
     * @var string
     */
    private $category;

    /**
     * @var User
     */
    private $user;

    /**
     * @param string $id
     * @param string $name
     * @param float $amount
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @param string $dueInterval
     * @param string $category
     * @param User $user
     */
    public function __construct(
        string $id,
        string $name,
        float $amount,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $dueInterval,
        string $category,
        User $user
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dueInterval = $dueInterval;
        $this->category = $category;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
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


    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }



}
