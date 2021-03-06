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
    protected function loadTokens(): bool
    {
        return false;
    }

    /**
     * @param array $response
     */
    protected function updateTokens(array $response): void
    {
        $this->accessToken = $response['access_token'];
        $this->refreshToken = $response['refresh_token'];
    }

    /**
     * @param string $url
     * @param array $query
     * @return ResponseInterface
     * @throws
     */
    public function get(string $url, array $query = []): ResponseInterface
    {
        try {
            return $this->getGuzzle()->get($url, [
                'query' => $query,
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh($throwable)->get($url, $query);
        }
    }

    /**
     * @param string $url
     * @param array $body
     * @param array $query
     * @return ResponseInterface
     * @throws
     */
    public function post(string $url, array $body = [], array $query = []): ResponseInterface
    {
        try {
            return $this->getGuzzle()->post($url, [
                'form_params' => $body,
                'query' => $query,
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh($throwable)->post($url, $body);
        }
    }

    /**
     * @param string $url
     * @param array $query
     * @return ResponseInterface
     * @throws
     */
    public function delete(string $url): ResponseInterface
    {
        try {
            return $this->getGuzzle()->delete($url, [
                'headers' => $this->getHeaders(),
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh($throwable)->delete($url);
        }
    }

    /**
     * @param string $url
     * @param resource $resource
     * @param string|null $uuid
     * @return ResponseInterface
     * @throws
     */
    public function upload(string $url, $resource, ?string $uuid = null): ResponseInterface
    {
        $headers = $this->getHeaders();
        
        if ($uuid) {
            $headers['Idempotency-Key'] = $uuid;
        }

        try {
            return $this->getGuzzle()->post($url, [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $resource,
                    ],
                ],
            ]);
        } catch (\Throwable $throwable) {
            return $this->refresh($throwable)->upload($url, $resource, $uuid);
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
                'base_uri' => $this->credentials->getBaseUrl()
            ]);
        }

        return $this->guzzle;
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        if (!$this->loadTokens()) {
            $this->login();
        }
    }

    /**
     * @return void
     */
    protected function login(): void 
    {
        $this->passport([
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password,
        ]);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return static
     * @throws
     */
    protected function refresh(\Throwable $throwable): self
    {
        if (!$this->refreshToken || $this->attempts > 3) {
            throw new \RuntimeException('The number of attempts is over');
        }

        if ($throwable->getCode() !== 401) {
            throw $throwable;
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

        $this->updateTokens(
            \json_decode($request->getBody(), true)
        );
    }

}
