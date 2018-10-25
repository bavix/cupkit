<?php

namespace Bavix\CupKit;

class Client
{

    /**
     * @var Identity
     */
    protected $identity;

    /**
     * Client constructor.
     * @param Identity $identity
     */
    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
    }

    public function getBuckets()
    {
        return $this->identity->get('/api/bucket');
    }

}
