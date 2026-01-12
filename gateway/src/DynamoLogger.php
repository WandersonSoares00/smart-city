<?php

namespace Gateway;

use Aws\DynamoDb\DynamoDbClient;

class DynamoLogger
{
    private DynamoDbClient $client;
    private string $table;

    public function __construct()
    {
        $this->client = new DynamoDbClient([
            'region'  => $_ENV['AWS_REGION'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

        $this->table = $_ENV['DYNAMODB_TABLE'];
    }

    public function logCommand(array $data): void
    {
        $this->client->putItem([
            'TableName' => $this->table,
            'Item' => [
                'pk' => ['S' => 'DEVICE#' . $data['device']],
                'sk' => ['S' => 'CMD#' . $data['timestamp']],
                'timestamp' => ['S' => $data['timestamp']],
                'source' => ['S' => $data['source']],
                'action' => ['S' => $data['action']],
                'value' => ['S' => (string) ($data['value'] ?? '')],
                'status' => ['S' => $data['status']],
                'metadata' => ['M' => $this->map($data['metadata'] ?? [])]
            ]
        ]);
    }

    private function map(array $data): array
    {
        $mapped = [];
        foreach ($data as $key => $value) {
            $mapped[$key] = ['S' => (string) $value];
        }
        return $mapped;
    }
}
