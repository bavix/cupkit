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

    /**
     * @return \Generator
     */
    public function getBuckets(): \Generator
    {
        return $this->each('/api/bucket');
    }

    /**
     * @param string $name
     * @param array $include
     * @return array
     */
    public function getBucket(string $name, array $include = []): array
    {
        return $this->single(\sprintf('/api/bucket/%s', $name), \compact($include));
    }

    /**
     * @param string $bucket
     * @return \Generator
     */
    public function getImages(string $bucket): \Generator
    {
        return $this->each(\sprintf('/api/bucket/%s/image', $bucket));
    }

    /**
     * @param string $bucket
     * @return \Generator
     */
    public function getViews(string $bucket): \Generator
    {
        return $this->each(\sprintf('/api/bucket/%s/view', $bucket));
    }

    /**
     * @param string $path
     * @param array $query
     * @param int $page
     * @return \Generator
     */
    protected function each(string $path, array $query = [], int $page = 1): \Generator
    {
        $query = \array_merge($query, \compact('page'));

        do {
            $response = $this->identity->get($path, $query);
            $received = new Received($response);
            $data = $received->asArray();

            foreach ($data['data'] as $bucket) {
                yield $bucket;
            }

        } while (++$page < $data['last_page']);
    }

    /**
     * @param string $path
     * @param array $includes
     * @return array
     */
    protected function single(string $path, array $includes): array
    {
        $include = \implode(',', $includes);
        $response = $this->identity->get($path, [
            'query' => \compact('include')
        ]);

        $received = new Received($response);
        return $received->asArray();
    }

}
