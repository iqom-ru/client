<?php

namespace Iqom\IqomClient;

use Enqueue\Dsn\Dsn;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class IqomClient
{

    private string $apiKey;
    private string $apiUrl;
    private int $timeout;
    private ?Client $client = null;

    public function __construct(string $dsn)
    {
        $dsn = Dsn::parseFirst($dsn);
        /** @var Dsn $dsn */
        $this->apiKey = $dsn->getPassword();
        $this->timeout = $dsn->getQueryBag()->getDecimal('timeout', 0);
        $this->apiUrl = $dsn->getScheme() . '://' . $dsn->getHost() . '/' . $dsn->getUser() . '/';
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

    public function makeRequest(IqomRequest $request): IqomResponse
    {
        return $this->makeManualRequest(
            $request->getAction(),
            $request->getParams()
        );
    }

    public function makeManualRequest(string $action, array $params = []): IqomResponse
    {
        try {
            $response = $this->getClient()->request(
                empty($params) ? 'GET' : 'POST',
                $this->apiUrl . $action,
                [
                    RequestOptions::CONNECT_TIMEOUT => 3,
                    RequestOptions::TIMEOUT => $this->timeout,
                    RequestOptions::JSON => $params,
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

}