<?php

namespace App\Model\Entity;


class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @var Entry[]
     */
    private $entries;

    /**
     * @var Contract[]
     */
    private $contracts;

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param \DateTimeInterface $createdAt
     * @param Entry[] $entries
     * @param Contract[] $contracts
     * @param string $token
     */
    public function __construct(
        string $id,
        string $firstName,
        string $lastName,
        string $email,
        \DateTimeInterface $createdAt,
        array $entries,
        array $contracts,
        string $token
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->entries = $entries;
        $this->contracts = $contracts;
        $this->token = $token;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return Contract[]
     */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }


}
