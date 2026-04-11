.PHONY: help \
	backend-setup backend-dev backend-test backend-migrate backend-fresh backend-seed backend-clear backend-install \
	frontend-dev frontend-build frontend-start frontend-lint frontend-install \
	dev install

BACKEND_DIR := backend
FRONTEND_DIR := frontend

help:
	@echo "Usage: make <target>"
	@echo ""
	@echo "Top-level targets:"
	@echo "  dev              Start both backend and frontend dev servers (parallel)"
	@echo "  install          Install dependencies for both backend and frontend"
	@echo ""
	@echo "Backend targets (Laravel):"
	@echo "  backend-setup    Full backend setup (install, .env, key, migrate, build)"
	@echo "  backend-dev      Start backend dev server (artisan serve + queue + logs + vite)"
	@echo "  backend-test     Run PHPUnit tests"
	@echo "  backend-migrate  Run database migrations"
	@echo "  backend-fresh    Fresh migration with seeders"
	@echo "  backend-seed     Run database seeders"
	@echo "  backend-clear    Clear all Laravel caches"
	@echo "  backend-install  Install Composer dependencies"
	@echo ""
	@echo "Frontend targets (Next.js):"
	@echo "  frontend-dev     Start Next.js dev server"
	@echo "  frontend-build   Build Next.js for production"
	@echo "  frontend-start   Start Next.js production server"
	@echo "  frontend-lint    Run ESLint"
	@echo "  frontend-install Install npm dependencies"

# ─── Top-level ────────────────────────────────────────────────────────────────

dev:
	cd $(BACKEND_DIR) && php artisan serve &
	cd $(FRONTEND_DIR) && npm run dev

install: backend-install frontend-install

# ─── Backend ──────────────────────────────────────────────────────────────────

backend-setup:
	cd $(BACKEND_DIR) && composer run setup

backend-dev:
	cd $(BACKEND_DIR) && php artisan serve

backend-test:
	cd $(BACKEND_DIR) && composer run test

backend-migrate:
	cd $(BACKEND_DIR) && php artisan migrate

backend-fresh:
	cd $(BACKEND_DIR) && php artisan migrate:fresh --seed

backend-seed:
	cd $(BACKEND_DIR) && php artisan db:seed

backend-clear:
	cd $(BACKEND_DIR) && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

backend-install:
	cd $(BACKEND_DIR) && composer install

# ─── Frontend ─────────────────────────────────────────────────────────────────

frontend-dev:
	cd $(FRONTEND_DIR) && npm run dev

frontend-build:
	cd $(FRONTEND_DIR) && npm run build

frontend-start:
	cd $(FRONTEND_DIR) && npm run start

frontend-lint:
	cd $(FRONTEND_DIR) && npm run lint

frontend-install:
	cd $(FRONTEND_DIR) && npm install
