<?php
require_once __DIR__ . '/../../vendor/autoload.php';
class MongoSessionHandler implements \SessionHandlerInterface
{
    private $collection;

    public function __construct($uri, $dbName = 'crm', $collection = 'sessions')
    {
        $client = new \MongoDB\Client($uri);
        $this->collection = $client->$dbName->$collection;
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
