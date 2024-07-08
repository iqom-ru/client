<?php

namespace Iqom\IqomClient;

use Enqueue\Dsn\Dsn;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class IqomClient
{

    private string $apiKey;
    private string $apiUrl;
    private int $timeout;
    private ?Client $client = null;
    private LoggerInterface $logger;

    public function __construct(
        string $dsn,
        ?LoggerInterface $logger = null
    )
    {
        $this->logger = $logger ?: new NullLogger();
        $dsn = Dsn::parseFirst($dsn);
        /** @var Dsn $dsn */
        $this->apiKey = $dsn->getPassword();
        $this->timeout = $dsn->getQueryBag()->getDecimal('timeout', 0);
        $this->apiUrl =
            $dsn->getScheme() . '://' .
            $dsn->getHost() .
            ($dsn->getPort() ? ':' . $dsn->getPort() : '') .
            '/' . $dsn->getUser() . '/'
        ;
        $this->logger->debug('IQOM API client created');
        $this->logger->debug('API URL: ' . $this->apiUrl);
        $this->logger->debug('Timeout: ' . $this->timeout);
    }

    protected function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client([
                'timeout'           => $this->timeout,
                'allow_redirects'   => false
            ]);
            $this->logger->debug('IQOM API client: Guzzle client created');
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
            $this->logger->debug('IQOM API client making request to API with action "' . $action . '"');
            $this->logger->debug('Params: ' . json_encode($params));
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
            $this->logger->error('GuzzleException #' . $e->getCode() . ': ' . $e->getMessage());
            return IqomResponse::createOnError(
                $e->getMessage()
            );
        }
        $apiResponse = IqomResponse::createWithResponse($response);
        $this->logger->debug('IQOM API client response status: ' . $apiResponse->getStatus());
        if ($apiResponse->isError()) {
            $this->logger->debug('IQOM API client error: ' . $apiResponse->getError());
        } elseif ($apiResponse->isSuccess()) {
            $this->logger->debug('Response data: ' . json_encode($apiResponse->getData()));
        }
        return $apiResponse;
    }

}