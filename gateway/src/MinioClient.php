<?php

namespace Gateway;

use Aws\S3\S3Client;

class MinioClient
{
    private S3Client $client;
    private string $bucket;

    public function __construct()
    {
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $_ENV['MINIO_ENDPOINT'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $_ENV['MINIO_KEY'],
                'secret' => $_ENV['MINIO_SECRET'],
            ],
        ]);

        $this->bucket = $_ENV['MINIO_BUCKET'];
        $this->ensureBucket();
    }

    private function ensureBucket(): void
    {
        try {
            $this->client->headBucket(['Bucket' => $this->bucket]);
        } catch (\Exception $e) {
            $this->client->createBucket(['Bucket' => $this->bucket]);
        }
    }

    public function upload(string $key, string $content): string
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $content,
            'ContentType' => 'image/jpeg'
        ]);

        return $_ENV['MINIO_ENDPOINT'] . "/{$this->bucket}/{$key}";
    }
}
