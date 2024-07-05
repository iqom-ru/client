<?php

namespace Iqom\IqomClient;

class IqomRequest
{

    const ACTION_TEST = 'test';
    const ACTION_CHECK_PENDING_REQUEST = 'check-pending-request';


    public static function makeTestRequest(): self
    {
        return new self(
            self::ACTION_TEST
        );
    }

    public static function makeCheckPendingRequest(string $requestId): self
    {
        return new self(
            self::ACTION_CHECK_PENDING_REQUEST,
            [
                'id' => $requestId
            ]
        );
    }


    private string $action;
    private array $params;

    public function __construct(string $action, array $params = [])
    {
        $this->action = $action;
        $this->params = $params;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParams(): array
    {
        return $this->params;
    }

}