.PHONY: help up down restart reset logs shell-backend shell-frontend migrate seed \
        test-backend test-frontend test-e2e tests \
        cs-fix cs-check phpcs phpcbf phpstan phpmd arkitect audit quality

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
	@echo "  make seed           Create the default admin user (idempotent)"
	@echo "  make test-backend   Run PHPUnit tests"
	@echo "  make test-frontend  Run Vitest unit tests"
	@echo "  make test-e2e       Run Playwright E2E tests"
	@echo "  make tests          Run all tests"
	@echo ""
	@echo "  ── Code Quality (NECROCON) ───────────────────────────────"
	@echo "  make cs-fix         Auto-fix formatting with PHP CS Fixer"
	@echo "  make cs-check       Check formatting only (no writes)"
	@echo "  make phpcs          PHP_CodeSniffer check"
	@echo "  make phpcbf         PHP_CodeSniffer auto-fix"
	@echo "  make phpstan        Static analysis (level 8)"
	@echo "  make phpmd          Mess detector (complexity, naming)"
	@echo "  make arkitect       Architecture layer rule checks"
	@echo "  make audit          Composer security advisory audit"
	@echo "  make quality        Run ALL quality checks at once"
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

seed:
	docker compose exec backend php bin/console janus:create-admin

test-backend:
	docker compose exec backend php bin/phpunit

test-frontend:
	docker compose exec frontend npm run test

test-e2e:
	docker compose exec frontend npm run e2e

tests: test-backend test-frontend test-e2e

# ── Code Quality (NECROCON) ───────────────────────────────────────────────

cs-fix:
	docker compose exec backend composer run cs-fix

cs-check:
	docker compose exec backend composer run cs-check

phpcs:
	docker compose exec backend composer run phpcs

phpcbf:
	docker compose exec backend composer run phpcbf

phpstan:
	docker compose exec backend composer run phpstan

phpmd:
	docker compose exec backend composer run phpmd

arkitect:
	docker compose exec backend composer run phparkitect

audit:
	docker compose exec backend composer run audit

quality:
	docker compose exec backend composer run quality
