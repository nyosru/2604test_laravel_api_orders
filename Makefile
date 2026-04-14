.PHONY: start serve swagger queue queue-logs lint

start:
	cp -n .env.example .env || true
	docker compose up --build -d
	@echo "❤️ ++ Докер контейнеры запущены ( Ларавель, БД, Редис для очередей, Ларавель что обрабатывает очереди"
	@sleep 3
	docker compose exec php composer i
	@sleep 1
	docker compose exec php php artisan key:generate --force
	docker compose exec php php artisan l5-swagger:generate
	@echo "❤️ ++ Свагер готов"
	docker compose exec php php artisan migrate:fresh --seed --force
	@echo "❤️ ++ Миграции обновлены, БД засеяна данными"
	@sleep 2
	make serve

serve:
	#docker compose exec php php artisan serve --host=127.0.0.1 --port=8000
	docker compose exec php php artisan serve

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
