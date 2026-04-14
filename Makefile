.PHONY: serve swagger

serve:
	docker compose exec php php artisan serve --host=0.0.0.0 --port=8000

swagger:
	php artisan l5-swagger:generate

bash:
	docker exec -it laravel_orders_php bash
