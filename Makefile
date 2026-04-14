.PHONY: start serve swagger queue queue-logs lint

start:
	cp .env.example .env
	docker compose up --build -d
	@echo "❤️ ++ Докер контейнеры запущены ( Ларавель, БД, Редис для очередей, Ларавель что обрабатывает очереди"
	docker compose exec php php artisan key:generate
	docker compose exec php php artisan l5-swagger:generate
	@echo "❤️ ++ Свагер готов"
	docker compose exec php php artisan migrate:fresh --seed
	@echo "❤️ ++ Миграции обновлены, БД засеяна данными"


serve:
	docker compose exec php php artisan serve --host=0.0.0.0 --port=8000

swagger:
	php artisan l5-swagger:generate

queue:
	docker compose up -d queue

queue-logs:
	docker compose logs -f queue

lint:
	docker compose exec php ./vendor/bin/pint

bash:
	docker exec -it laravel_orders_php bash
