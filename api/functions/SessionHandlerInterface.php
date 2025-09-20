<?php

use SessionHandlerInterface;

class MongoSessionHandler implements SessionHandlerInterface
{
    private $collection;

    public function __construct($uri, $collection = 'sessions')
    {
        $client = new MongoDB\Client($uri);
        $this->collection = $client->$collection;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        $session = $this->collection->findOne(['_id' => $id]);
        return $session['data'] ?? '';
    }

    public function write(string $id, string $data): bool
    {
        $this->collection->updateOne(
            ['_id' => $id],
            [
                '$set' => [
                    'data' => $data,
                    'last_access' => new MongoDB\BSON\UTCDateTime()
                ]
            ],
            ['upsert' => true]
        );
        return true;
    }

    public function destroy(string $id): bool
    {
        $this->collection->deleteOne(['_id' => $id]);
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $expire = new MongoDB\BSON\UTCDateTime((time() - $max_lifetime) * 1000);
        $result = $this->collection->deleteMany(['last_access' => ['$lt' => $expire]]);
        return $result->getDeletedCount();
    }
}

$uri = 'mongodb+srv://' . $_ENV['MDB_USER'] . ':' . $_ENV['MDB_PASS'] . '@' . $_ENV['ATLAS_CLUSTER_SRV'] . '/?retryWrites=true&w=majority&appName=' . $_ENV['APP_NAME'];
$handler = new MongoSessionHandler($uri);
session_set_save_handler($handler, true);
