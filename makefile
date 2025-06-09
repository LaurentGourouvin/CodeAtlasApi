# Makefile for CodeAtlas - Symfony + Docker stack

PROJECT_NAME=codeatlas
COMPOSE=docker compose

.DEFAULT_GOAL := help

install: ## 🚀 Build and start all containers
	@echo "🚀 Building and starting containers..."
	$(COMPOSE) up --build -d

stop: ## 🛑 Stop running containers
	@echo "🛑 Stopping containers..."
	$(COMPOSE) stop

delete: ## 🔥 Remove containers and volumes created by docker-compose
	@echo "🔥 Deleting containers and volumes..."
	$(COMPOSE) down --volumes --remove-orphans

link: ## 🔗 Display available URLs for local services
	@echo ""
	@echo "🔗 Access your local services:"
	@echo "-----------------------------------"
	@printf "🚀  %-22s → %s\n" "Symfony API" "http://localhost:8088"
	@printf "📦  %-22s → %s\n" "Adminer (DB UI)" "http://localhost:8080"
	@printf "📘  %-22s → %s\n" "Swagger API Platform" "http://localhost:8088/api"
	@printf "🐘  %-22s → %s\n" "PostgreSQL" "used internally by Symfony"
	@echo "-----------------------------------"

status: ## 📊 Show container ID, image, name and status with colors
	@echo ""
	@echo "📊 Container status for project: $(PROJECT_NAME)"
	@echo "-------------------------------------------------------------"
	@docker ps -a --filter "name=$(PROJECT_NAME)" --format '{{.ID}} {{.Image}} {{.Names}} {{.Status}}' | while read -r id image name status1 status2 status3; do \
		status="$$status1 $$status2 $$status3"; \
		if echo "$$status" | grep -q Up; then \
			printf "✅ \033[32m%-12s %-25s %-25s %-30s\033[0m\n" "$$id" "$$image" "$$name" "$$status"; \
		else \
			printf "❌ \033[31m%-12s %-25s %-25s %-30s\033[0m\n" "$$id" "$$image" "$$name" "$$status"; \
		fi; \
	done

down: ## 🧯 Stop a specific container (e.g. make down service=adminer)
	@if [ -z "$(service)" ]; then \
		echo "❌ Please provide a service name: make down service=adminer"; \
		exit 1; \
	fi; \
	echo "🛑 Stopping service: $(service)..."; \
	docker compose stop $(service)

restart: ## ♻️ Restart a specific container (e.g. make restart service=api)
	@if [ -z "$(service)" ]; then \
		echo "❌ Please provide a service name: make restart service=api"; \
		exit 1; \
	fi; \
	echo "🔄 Restarting service: $(service)..."; \
	docker compose restart $(service)

restart-all: ## ♻️ Restart all containers
	@echo "🔄 Restarting all containers..."
	docker compose restart

logs: ## 📜 Show logs of a specific container (e.g. make logs service=api)
	@if [ -z "$(service)" ]; then \
		echo "❌ Please provide a service name: make logs service=api"; \
		exit 1; \
	fi; \
	echo "📜 Showing logs for service: $(service)..."; \
	docker compose logs -f --tail=50 $(service)

api-logs: ## 📄 Show Symfony API logs (dev environment)
	@echo "📄 Reading Symfony API logs (dev)..."
	docker compose exec api tail -f var/log/dev.log


connect: ## 🐚 Open a shell in a specific container (e.g. make connect service=api)
	@if [ -z "$(service)" ]; then \
		echo "❌ Please provide a service name: make connect service=api"; \
		exit 1; \
	fi; \
	echo "🐚 Connecting to container: $(service)..."; \
	if docker compose exec $(service) bash -c "echo" >/dev/null 2>&1; then \
		docker compose exec $(service) bash; \
	else \
		docker compose exec $(service) sh; \
	fi

run-test: ## 🧪 Run PHPUnit tests from inside the api container
	@echo "🧪 Running tests in 'api' container..."
	docker compose exec api php bin/phpunit

help: ## 📖 Show available Make commands
	@echo ""
	@echo "📘 Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?##' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'
