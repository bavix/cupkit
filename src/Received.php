<?php

namespace Bavix\CupKit;

use Psr\Http\Message\ResponseInterface;

class Received
{

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $data;

    /**
     * Received constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        if (!$this->data) {
            $this->data = \json_decode($this->response->getBody(), true);
        }
        return $this->data;
    }

}
