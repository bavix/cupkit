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
        $this->data = \json_decode($response->getBody(), true);
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->data;
    }

}
