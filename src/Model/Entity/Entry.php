<?php

namespace App\Model\Entity;


class Entry
{
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
    private $date;

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
     * @param \DateTimeInterface $date
     * @param string $category
     * @param User $user
     */
    public function __construct(string $id, string $name, float $amount, \DateTimeInterface $date, string $category, User $user)
    {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->date = $date;
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
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
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
