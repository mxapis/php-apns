<?php declare(strict_types=1);

namespace MXAPIS\APNS;

use DateTimeImmutable;

class Response
{
    const STATUS_SUCCESS = 200;
    const STATUS_UNREGISTERED = 410;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $reason;

    /** @var string */
    protected $apnsId;

    /** @var \DateTimeImmutable */
    protected $timestamp;

    /** @var string */
    protected $identifier;

    public function __construct(int $statusCode, string $apnsId)
    {
        $this->statusCode = $statusCode;
        $this->apnsId = $apnsId;
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function setApnsId(string $apnsId): void
    {
        $this->apnsId = $apnsId;
    }

    public function setTimestamp(DateTimeImmutable $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function apnsId(): string
    {
        return $this->apnsId;
    }

    public function timestamp(): ?DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function success(): bool
    {
        return $this->statusCode === self::STATUS_SUCCESS;
    }
}