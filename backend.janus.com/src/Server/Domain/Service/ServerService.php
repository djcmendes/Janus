<?php

declare(strict_types=1);

namespace App\Server\Domain\Service;

use Doctrine\DBAL\Connection;

/**
 * Aggregates server health and info data.
 */
final class ServerService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string     $redisUrl,
        private readonly string     $rabbitmqDsn,
    ) {}

    /**
     * Returns basic application/server information.
     *
     * @return array<string, mixed>
     */
    public function getInfo(): array
    {
        return [
            'project_name'        => 'Janus',
            'version'             => '1.0.0',
            'php_version'         => PHP_VERSION,
            'max_upload_size'     => ini_get('upload_max_filesize'),
            'rate_limiter_enabled' => false,
        ];
    }

    /**
     * Runs connectivity checks against MariaDB, Redis, and RabbitMQ.
     *
     * Returns an array with keys 'database', 'redis', 'rabbitmq'.
     * Each value is either 'ok' or an error string.
     *
     * @return array<string, string>
     */
    public function getHealth(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'redis'    => $this->checkRedis(),
            'rabbitmq' => $this->checkRabbitMq(),
        ];
    }

    private function checkDatabase(): string
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return 'ok';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    private function checkRedis(): string
    {
        // $redisUrl is in the form  redis://:password@host:port
        $parsed = parse_url($this->redisUrl);
        if ($parsed === false) {
            return 'invalid REDIS_URL';
        }

        $host     = $parsed['host'] ?? 'redis';
        $port     = $parsed['port'] ?? 6379;
        $password = isset($parsed['pass']) ? urldecode($parsed['pass']) : null;

        try {
            $redis = new \Redis();
            $redis->connect($host, (int) $port, 2.0);

            if ($password !== null && $password !== '') {
                $redis->auth($password);
            }

            $redis->ping();

            return 'ok';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    private function checkRabbitMq(): string
    {
        // $rabbitmqDsn is in the form  amqp://user:pass@host:port
        $parsed = parse_url($this->rabbitmqDsn);
        if ($parsed === false) {
            return 'invalid RABBITMQ_DSN';
        }

        $host = $parsed['host'] ?? 'rabbitmq';
        $port = $parsed['port'] ?? 5672;

        $socket = @fsockopen($host, (int) $port, $errno, $errstr, 2.0);
        if ($socket === false) {
            return sprintf('connection failed: %s (%d)', $errstr, $errno);
        }

        fclose($socket);

        return 'ok';
    }
}
