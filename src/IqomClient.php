<?php

namespace Leoza\IqomClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class IqomClient
{

    private string $apiKey;
    private string $apiUrl;
    private int $timeout;
    private ?Client $client = null;

    public function __construct(
        string $apiKey,
        int $timeout = 0,
        string $apiVersion = 'v1',
        string $apiHost = 'api.iqom.ru',
        string $apiSchema = 'https'
    )
    {
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->apiUrl = $apiSchema . '://' . $apiHost . '/' . $apiVersion . '/';
    }

    protected function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client([
                'timeout'           => $this->timeout,
                'allow_redirects'   => false
            ]);
        }
        return $this->client;
    }

    public function makeRequest(string $action, array $params = []): IqomResponse
    {
        try {
            $response = $this->getClient()->request(
                empty($params) ? 'GET' : 'POST',
                $this->apiUrl . $action,
                [
                    RequestOptions::CONNECT_TIMEOUT => 3,
                    RequestOptions::TIMEOUT => $this->timeout,
                    RequestOptions::JSON => json_encode($params),
                    RequestOptions::HEADERS => [
                        'X-Authenticate-Id' => $this->apiKey
                    ]
                ]
            );
        } catch (GuzzleException $e) {
            return IqomResponse::createOnError(
                $e->getMessage()
            );
        }
        return IqomResponse::createWithResponse($response);
    }

    public function checkPendingResponse(string|IqomResponse $pendingRequestId): IqomResponse
    {
        if ($pendingRequestId instanceof IqomResponse) {
            $pendingRequestId = $pendingRequestId->getPendingRequestId();
        }
        return $this->makeRequest('promise/update', ['id' => $pendingRequestId]);
    }

}