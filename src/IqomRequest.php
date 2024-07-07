<?php

namespace Iqom\IqomClient;

class IqomRequest
{

    const ACTION_TEST = 'test';
    const ACTION_CHECK_PENDING_REQUEST = 'check-pending-request';

    const ACTION_TRANSLATION_WRITE = 'translation/write';
    const ACTION_TRANSLATION_READ = 'translation/read';
    const ACTION_TRANSLATION_DELETE = 'translation/delete';


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

    public static function makeTranslationWriteRequest(string $projectId, array $translations): self
    {
        return new self(
            self::ACTION_TRANSLATION_WRITE,
            [
                'project_id' => $projectId,
                'translations' => $translations
            ]
        );
    }

    public static function makeTranslationReadRequest(string $projectId, array $domains, array $locales): self
    {
        return new self(
            self::ACTION_TRANSLATION_READ,
            [
                'project_id' => $projectId,
                'domains' => $domains,
                'locales' => $locales
            ]
        );
    }

    public static function makeTranslationDeleteRequest(string $projectId, array $keys): self
    {
        return new self(
            self::ACTION_TRANSLATION_DELETE,
            [
                'project_id' => $projectId,
                'keys' => $keys
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