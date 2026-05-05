.PHONY: help \
	backend-setup backend-dev backend-test backend-migrate backend-fresh backend-seed backend-clear backend-install \
	frontend-dev frontend-build frontend-install \
	dev install

APP_DIR := app

help:
	@echo "Usage: make <target>"
	@echo ""
	@echo "Top-level targets:"
	@echo "  dev              Start Laravel dev stack (serve, queue, logs, Vite via composer run dev)"
	@echo "  install          Install Composer and npm dependencies in app/"
	@echo ""
	@echo "Backend targets (Laravel, in app/):"
	@echo "  backend-setup    Full setup (composer setup: install, .env, key, migrate, npm, build)"
	@echo "  backend-dev      Start php artisan serve only"
	@echo "  backend-test     Run PHPUnit tests"
	@echo "  backend-migrate  Run database migrations"
	@echo "  backend-fresh    Fresh migration with seeders"
	@echo "  backend-seed     Run database seeders"
	@echo "  backend-clear    Clear all Laravel caches"
	@echo "  backend-install  Install Composer dependencies"
	@echo ""
	@echo "Frontend targets (Vite, in app/):"
	@echo "  frontend-dev     Start Vite dev server (use with backend-dev, or use: make dev)"
	@echo "  frontend-build   Production Vite build"
	@echo "  frontend-install Install npm dependencies"

# ─── Top-level ────────────────────────────────────────────────────────────────

dev:
	cd $(APP_DIR) && composer run dev

install: backend-install frontend-install

# ─── Backend ──────────────────────────────────────────────────────────────────

backend-setup:
	cd $(APP_DIR) && composer run setup

backend-dev:
	cd $(APP_DIR) && php artisan serve

backend-test:
	cd $(APP_DIR) && composer run test

backend-migrate:
	cd $(APP_DIR) && php artisan migrate

backend-fresh:
	cd $(APP_DIR) && php artisan migrate:fresh --seed

backend-seed:
	cd $(APP_DIR) && php artisan db:seed

backend-clear:
	cd $(APP_DIR) && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

backend-install:
	cd $(APP_DIR) && composer install

# ─── Frontend (Vite) ──────────────────────────────────────────────────────────

frontend-dev:
	cd $(APP_DIR) && npm run dev

frontend-build:
	cd $(APP_DIR) && npm run build

frontend-install:
	cd $(APP_DIR) && npm install
