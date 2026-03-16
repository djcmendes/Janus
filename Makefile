.PHONY: help up down restart reset logs shell-backend shell-frontend migrate test-backend test-frontend

# Default target
help:
	@echo ""
	@echo "Janus Platform — Makefile"
	@echo "─────────────────────────────────────"
	@echo "  make up             Start the full stack (detached)"
	@echo "  make down           Stop all services"
	@echo "  make restart        Restart all services"
	@echo "  make reset          Tear down + remove volumes + rebuild"
	@echo "  make logs           Tail all service logs"
	@echo "  make shell-backend  Open a shell in the backend container"
	@echo "  make shell-frontend Open a shell in the frontend container"
	@echo "  make migrate        Run Doctrine database migrations"
	@echo "  make test-backend   Run PHPUnit tests"
	@echo "  make test-frontend  Run Vitest unit tests"
	@echo "  make test-e2e       Run Playwright E2E tests"
	@echo "  make tests          Run all tests"
	@echo ""

up:
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose down && docker compose up -d --build

reset:
	docker compose down -v --remove-orphans
	docker compose up -d --build

logs:
	docker compose logs -f

shell-backend:
	docker compose exec backend bash

shell-frontend:
	docker compose exec frontend sh

migrate:
	docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

test-backend:
	docker compose exec backend php bin/phpunit

test-frontend:
	docker compose exec frontend npm run test

test-e2e:
	docker compose exec frontend npm run e2e

tests: test-backend test-frontend test-e2e
