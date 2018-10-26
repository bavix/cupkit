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
        return $this->fetch(\sprintf('/api/bucket/%s', $name), \compact($include));
    }

    /**
     * @param string $name
     * @return array
     */
    public function createBucket(string $name): array
    {
        return $this->create('/api/bucket', \compact('name'));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function dropBucket(string $name): bool
    {
        return $this->delete(\sprintf('/api/bucket/%s', $name));
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
     * @param string $bucket
     * @param string $name
     * @param array $include
     * @return array
     */
    public function getView(string $bucket, string $name, array $include = []): array
    {
        return $this->fetch(\sprintf('/api/bucket/%s/view/%s', $bucket, $name), \compact($include));
    }

    /**
     * @param string $bucket
     * @param array $data
     * @return array
     */
    public function createView(string $bucket, array $data): array
    {
        return $this->create(\sprintf('/api/bucket/%s/view', $bucket), $data);
    }

    /**
     * @param string $bucket
     * @param string $name
     * @return bool
     */
    public function dropView(string $bucket, string $name): bool
    {
        return $this->delete(\sprintf('/api/bucket/%s/view/%s', $bucket, $name));
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
     * @param string $uuid
     * @param array $include
     * @return array
     */
    public function getImage(string $bucket, string $uuid, array $include = []): array
    {
        return $this->fetch(\sprintf('/api/bucket/%s/image/%s', $bucket, $uuid), $include);
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
    protected function fetch(string $path, array $includes): array
    {
        $include = \implode(',', $includes);
        $response = $this->identity->get($path, [
            'query' => \compact('include')
        ]);

        $received = new Received($response);
        return $received->asArray();
    }

    /**
     * @param string $path
     * @param array $data
     * @return array
     */
    protected function create(string $path, array $data): array
    {
        $response = $this->identity->post($path, $data);
        $received = new Received($response);
        return $received->asArray();
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function delete(string $path): bool
    {
        try {
            $this->identity->delete($path);
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

}
