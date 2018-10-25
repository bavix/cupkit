<?php

namespace Bavix\CupKit;

use Psr\Http\Message\ResponseInterface;

class Identity
{

    /**
     * @var int
     */
    protected $attempts = 0;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var ClientCredentials
     */
    protected $credentials;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * Config constructor.
     * @param ClientCredentials $credentials
     * @param string $username
     * @param string $password
     */
    public function __construct(ClientCredentials $credentials, string $username, string $password)
    {
        $this->credentials = $credentials;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return bool
     */
    protected function load(): bool
    {
        return false;
    }

    /**
     * @param string $url
     * @param array $query
     * @return ResponseInterface
     */
    public function get(string $url, array $query = []): ResponseInterface
    {
        try {
            return $this->getGuzzle()->get($url, [
                'query' => $query,
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh()->get($url, $query);
        }
    }

    /**
     * @param string $url
     * @param array $body
     * @param array $query
     * @return ResponseInterface
     */
    public function post(string $url, array $body = [], array $query = []): ResponseInterface
    {
        try {
            return $this->getGuzzle()->get($url, [
                'form_params' => $body,
                'query' => $query,
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh()->post($url, $body);
        }
    }

    /**
     * @param string $url
     * @param array $query
     * @return ResponseInterface
     */
    public function delete(string $url): ResponseInterface
    {
        try {
            return $this->getGuzzle()->delete($url, [
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh()->delete($url);
        }
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return string
     */
    protected function getAccessToken(): string
    {
        if (!$this->accessToken) {
            $this->initialize();
        }

        return $this->accessToken;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getGuzzle(): \GuzzleHttp\Client
    {
        if (!$this->guzzle) {
            $this->guzzle = new \GuzzleHttp\Client([
                'base_url' => $this->credentials->getBaseUrl()
            ]);
        }

        return $this->guzzle;
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        if (!$this->load()) {
            $this->passport([
                'grunt_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
            ]);
        }
    }

    /**
     * @return static
     */
    protected function refresh(): self
    {
        if (!$this->refreshToken || $this->attempts > 3) {
            throw new \RuntimeException();
        }

        try {
            $this->passport([
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ]);
        } catch (\Throwable $throwable) {
            $this->attempts++;
        }

        return $this;
    }

    /**
     * @param array $body
     * @return void
     */
    protected function passport(array $body): void
    {
        $request = $this->getGuzzle()->post('/oauth/token', [
            'auth' => [
                $this->credentials->getClientId(),
                $this->credentials->getClientSecret(),
            ],
            'form_params' => $body,
            'headers' => ['Accept' => 'application/json',],
        ]);

        $object = \json_decode((string)$request->getBody(), true);
        $this->accessToken = $object['access_token'];
        $this->refreshToken = $object['refresh_token'];
    }

}
