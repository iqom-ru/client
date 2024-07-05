<?php

namespace Iqom\IqomClient;

use Psr\Http\Message\ResponseInterface;

class IqomResponse
{

    const STATUS_PENDING = 'pending';
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    public static function createOnError(?string $error, ?string $id = null): self
    {
        $e = new self();
        $e->id = $id;
        $e->status = self::STATUS_ERROR;
        $e->error = $error;
        return $e;
    }

    public static function createOnPending(string $id): self
    {
        $e = new self();
        $e->id = $id;
        $e->status = self::STATUS_PENDING;
        return $e;
    }

    public static function createOnSuccess(string $id, array $data = []): self
    {
        $e = new self();
        $e->id = $id;
        $e->status = self::STATUS_SUCCESS;
        $e->data = $data;
        return $e;
    }

    public static function createWithResponse(ResponseInterface $response): self
    {
        $body = $response->getBody();
        $data = @json_decode($body, true);
        if (!is_array($data)) {
            $data = [];
        }
        $id = $data['id'] ?? null;
        if ($response->getStatusCode() == 102) {
            return self::createOnPending($id);
        }
        if ($response->getStatusCode() == 200) {
            return self::createOnSuccess($id, $data['data'] ?? []);
        }
        return self::createOnError($data['error'] ?? null, $id);
    }

    protected ?string $status = null;
    protected ?string $id = null;
    protected ?string $error = null;
    protected array $data = [];

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isError(): bool
    {
        return $this->status == self::STATUS_ERROR;
    }

    public function isSuccess(): bool
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getData(): array
    {
        return $this->data;
    }

}