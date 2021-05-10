<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;


use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class DynamoDBService
{
    private Marshaler $marshal;

    public function __construct(private DynamoDbClient $client)
    {
        $this->marshal = new Marshaler();
    }

    public function putItem(string $body): void
    {
        $item = $this->marshal->marshalJson($body);

        $params = [
            'TableName' => getenv('TWITTER_SEARCH_IMG_TABLE'),
            'Item' => $item,
        ];

        $this->client->putItem($params);
    }

    public function getAllItem():array
    {
        $data = $this->client->scan([
            'TableName' => getenv('TWITTER_SEARCH_IMG_TABLE'),
        ]);

        $result = [];
        foreach ($data->get('Items') AS $item) {
            $result = $this->marshal->unmarshalItem($item);
        }

        return $result;
    }

    public function getItem(array $params):array
    {
        $key = $this->marshal->marshalJson(\json_encode($params, flags: JSON_INVALID_UTF8_IGNORE));
        $data = $this->client->getItem([
            'TableName' => getenv('TWITTER_SEARCH_IMG_TABLE'),
            'Key' => $key,
        ]);

        return $this->marshal->unmarshalItem($data->get('Item'));
    }
}