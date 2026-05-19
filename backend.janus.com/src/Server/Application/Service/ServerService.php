<?php

/**
 * @file ServerService.php
 *
 * Domain service that aggregates server health and application info.
 *
 * @package App\Server\Application\Service
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Application\Service;

use Doctrine\DBAL\Connection;

/**
 * Aggregates server health and application information.
 *
 * Health checks run against MariaDB (via Doctrine DBAL), Redis (via ext-redis),
 * and RabbitMQ (via a raw TCP socket probe). Each check returns 'ok' on success
 * or an error string on failure — callers decide whether to surface a 503.
 */
final class ServerService
{
    /**
     * @param Connection $connection  Active DBAL connection used for the database health check.
     * @param string     $redisUrl    Redis DSN in the form redis://:password@host:port.
     * @param string     $rabbitmqDsn RabbitMQ DSN in the form amqp://user:pass@host:port.
     */
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

    /**
     * Executes `SELECT 1` against the DBAL connection.
     *
     * @return string 'ok' on success, or the exception message on failure.
     */
    private function checkDatabase(): string
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return 'ok';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Connects to Redis, optionally authenticates, and sends PING.
     *
     * @return string 'ok' on success, 'invalid REDIS_URL' if the DSN cannot be parsed,
     *                or the exception message on connection/auth failure.
     */
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

    /**
     * Probes the RabbitMQ AMQP port with a raw TCP socket (2-second timeout).
     *
     * @return string 'ok' on success, 'invalid RABBITMQ_DSN' if the DSN cannot be parsed,
     *                or a descriptive failure string on connection refusal.
     */
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
