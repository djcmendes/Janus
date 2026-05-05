# Janus — TODO

---

## Backend

### Exception Handling & Observability

#### [ ] Standardise uncaught exception responses
Currently, any exception that is not explicitly caught in a controller (e.g. a Doctrine
failure in `findPaginated()`) bypasses the project's JSON envelope and returns Symfony's
default error format, which is inconsistent with the `{ errors: [{ message, extensions: { code } }] }`
contract the frontend expects.

**Implementation:**
- Create `src/Shared/Presentation/EventSubscriber/ExceptionSubscriber.php`
- Listen on `KernelEvents::EXCEPTION`
- Map exception types to HTTP status codes and controlled error messages
- Always return the standard JSON envelope

#### [ ] Infrastructure exception logging to InfluxDB (separate channel)
Doctrine and other infrastructure exceptions should be routed to InfluxDB independently
of application-level logs so they can be monitored and alerted on separately.

**Implementation:**
1. Add `config/packages/monolog.yaml` with a dedicated `infrastructure` channel
2. Create `src/Shared/Log/InfluxDbHandler.php` — a custom Monolog handler that writes
   to InfluxDB using the Line Protocol
3. Register the handler as a Symfony service and wire it to the `infrastructure` channel
4. In `ExceptionSubscriber`, inject `LoggerInterface $infrastructureLogger`
   (autowired via `#[Autowire(service: 'monolog.logger.infrastructure')]`) and log
   infrastructure exceptions to that channel

**Files to create:**
```
src/Shared/Log/InfluxDbHandler.php
src/Shared/Presentation/EventSubscriber/ExceptionSubscriber.php
config/packages/monolog.yaml
```
