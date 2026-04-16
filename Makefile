.PHONY: start serve swagger queue queue-logs lint

start:
	cp -n .env.example .env || true
	docker compose up --build -d --remove-orphans
	@echo "❤️ ++ Докер контейнеры запущены ( Ларавель, БД, Редис для очередей, Ларавель что обрабатывает очереди"
	@sleep 3
	docker compose run --rm laravel_orders_php composer i --no-progress
	@sleep 1
	docker compose exec laravel_orders_php php artisan key:generate --force
	docker compose exec laravel_orders_php php artisan l5-swagger:generate
	@echo "❤️ ++ Свагер готов"
	docker compose exec laravel_orders_php php artisan migrate:fresh --seed --force
	@echo "❤️ ++ Миграции обновлены, БД засеяна данными"
	@echo "❤️ ++ Сайт доступен: http://localhost"
	@echo "❤️ ++ Swagger доступен: http://localhost/api/documentation"

serve:
	docker compose up -d nginx laravel_orders_php

swagger:
	php artisan l5-swagger:generate

queue:
	docker compose up -d queue

queue-logs:
	docker compose logs -f queue

lint:
	docker compose exec laravel_orders_php ./vendor/bin/pint

bash:
	docker exec -it laravel_orders_php bash
