<?php
namespace andrefelipe\Orchestrate\Objects;

use andrefelipe\Orchestrate\HttpClientInterface;

/**
 * Provides the bare basis, a connection to a HTTP service.
 */
abstract class AbstractConnection implements ConnectionInterface
{
    /**
     * @var HttpClientInterface
     */
    private $_httpClient;

    public function getHttpClient($required = false)
    {
        if ($required) {
            $this->noHttpClientException();
        }

        return $this->_httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }

    /**
     * @throws \BadMethodCallException if the http client is not set yet.
     */
    protected function noHttpClientException()
    {
        if (!$this->_httpClient) {
            throw new \BadMethodCallException('There is no HTTP client set yet. Please do so through setHttpClient() method.');
        }
    }
}