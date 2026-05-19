# Server Module

Provides server status and health-check endpoints.

---

## Directory Structure

```
Server/
  Domain/
    Service/
      ServerService.php           ← Aggregates server info and health-check results
      Tests/
        ServerServiceTest.php     ← Abstract base
        ServerServiceBaseTest.php
        ServerServiceGetInfoTest.php
        ServerServiceGetHealthTest.php
  Presentation/
    Controller/
      ServerController.php        ← HTTP controller (ping, info, health)
      Tests/
        ServerControllerTest.php  ← Abstract base
        ServerControllerBaseTest.php
        ServerControllerPingTest.php
        ServerControllerInfoTest.php
        ServerControllerHealthTest.php
```

---

## Endpoints

| Method | Path             | Scope         | Description                                   |
|--------|------------------|---------------|-----------------------------------------------|
| GET    | /server/ping     | PUBLIC        | Liveness probe — returns `{ "data": "pong" }` |
| GET    | /server/info     | AUTHENTICATED | Application and runtime information           |
| GET    | /server/health   | AUTHENTICATED | Infrastructure health checks (200 or 503)     |

---

## Response Shapes

### GET /server/ping
```json
{ "data": "pong" }
```

### GET /server/info
```json
{
  "data": {
    "project_name": "Janus",
    "version": "1.0.0",
    "php_version": "8.3.x",
    "max_upload_size": "100M",
    "rate_limiter_enabled": false
  }
}
```

### GET /server/health
Returns **200** when all services are healthy, **503** when any service is degraded.

```json
{
  "data": {
    "database": "ok",
    "redis": "ok",
    "rabbitmq": "ok"
  }
}
```

Each value is either `"ok"` or an error string describing the failure.

---

## ServerService Health Checks

| Check      | Mechanism                           | Error value                          |
|------------|-------------------------------------|--------------------------------------|
| `database` | `Connection::executeQuery('SELECT 1')` | Exception message                 |
| `redis`    | `ext-redis` PING with 2s timeout    | Parse error or exception message     |
| `rabbitmq` | TCP socket probe via `fsockopen`    | Parse error or connection fail string |

Both `redis` and `rabbitmq` return `'invalid REDIS_URL'` / `'invalid RABBITMQ_DSN'` when the respective DSN cannot be parsed by `parse_url()`.

---

## Authorization

`ping` uses `ApiScope::PUBLIC` — no authentication required.  
`info` and `health` use `ApiScope::AUTHENTICATED` — any authenticated client (WEB, IOS, ANDROID, CLI) may access them. Neither action requires `ROLE_ADMIN`.

---

## Dependencies

| Dependency        | Injected via      | Purpose                        |
|-------------------|-------------------|--------------------------------|
| `RequestGuard`    | Constructor       | Authentication / client checks |
| `ServerService`   | Constructor       | Info and health data           |
| `Connection`      | ServerService ctor | Database health check         |
| `string $redisUrl` | ServerService ctor | Redis DSN                    |
| `string $rabbitmqDsn` | ServerService ctor | RabbitMQ DSN              |
