<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## как запускать

на компе установлен и запущен докер
команды по очереди запускаем в терминале

```
git clone https://github.com/nyosru/2604test_laravel_api_orders
``` 

``` 
cd 2604test_laravel_api_orders
``` 

``` 
make start
```



## сайт откроется на localhost

после `make start` проект будет доступен по адресу:

http://localhost

swagger для ручной проверки API:

http://localhost/api/documentation

## пробуем API

и готово :)

    Я Сергей 
https://php-cat.com

мой вк для связи https://vk.com/phpcatcom

телефон 89-222-6-222-89

телеграм https://t.me/phpcatcom


# ----- тест задание ----

# Тестовое задание: API сервиса управления заказами

**Время:** 3–4 часа  
**Стек:** PHP 8.4+, Laravel 12+, MySQL или PostgreSQL, Redis, Docker

---

## Легенда

Интернет-магазин запчастей нуждается в backend-сервисе для управления заказами. Каталог товаров заполняется через сидер - CRUD для товаров реализовывать не нужно.

---

## 1. Модели и структура данных

Спроектировать схему БД и написать миграции:

- **products** - id, name, sku, price, stock_quantity, category
- **customers** - id, name, email, phone
- **orders** - id, customer_id, status, total_amount, confirmed_at, shipped_at
- **order_items** - id, order_id, product_id, quantity, unit_price, total_price

Статусы заказа: `new → confirmed → processing → shipped → completed`. Отдельно доступен переход в `cancelled` из статусов `new` и `confirmed`.

Написать фабрики и сидер с тестовыми данными (товары в нескольких категориях, несколько клиентов).

---

## 2. REST API (`/api/v1/`)

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/products` | Список товаров. Фильтрация по category, поиск по name/sku, пагинация. |
| POST | `/orders` | Создание заказа (customer_id, массив items). Валидация остатков, атомарное списание stock через транзакцию. |
| GET | `/orders` | Список заказов. Фильтры: status, customer_id, диапазон дат. Items и customer подгружаются без N+1. |
| GET | `/orders/{id}` | Детали заказа. |
| PATCH | `/orders/{id}/status` | Смена статуса с валидацией допустимых переходов. |

### Требования

- **Resource-классы** для ответов
- **Form Request** для валидации
- **Единообразная обработка ошибок** — JSON, корректные HTTP-коды
- **Rate limiting** на создание заказов (не более 10 в минуту по IP)

---

## 3. Service Layer

Логика создания заказа и смены статуса - в `OrderService`. Контроллер не содержит бизнес-логику.

---

## 4. Очередь

При переходе заказа в статус `confirmed` - диспатчить джобу `ExportOrderJob`:

- Имитирует отправку данных во внешнюю систему: `Http::post()` (https://httpbin.org/) на URL из конфига (в тестах - `Http::fake()`)
- 3 попытки при неудаче (`$tries = 3`)
- Очередь через Redis

---

## 5. Тесты

Минимум:

- **Feature**: успешное создание заказа - stock уменьшился, total посчитан, ответ корректный
---

## 6. Docker и запуск

`docker-compose.yml` с контейнерами: app, nginx, postgres, redis.

В `README.md` описать: как поднять проект, накатить миграции, запустить очередь и тесты.

---

## Чего мы НЕ ждём

- Фронтенда
- Полного покрытия тестами - достаточно показать подход

---

## Критерии оценки

| Область | На что смотрим |
|---------|----------------|
| Архитектура | Service Layer, тонкие контроллеры, разделение ответственности |
| Eloquent | Связи, скоупы, eager loading, отсутствие N+1 |
| API | Resource-классы, консистентные ответы, HTTP-коды |
| БД | Миграции, индексы, транзакция при создании заказа |
| Очереди | Рабочая джоба с retry |
| Docker | Поднимается с первого раза |
| Git | Логичные коммиты |
| Код | Типизация, читаемость, чистота |

---

## Бонусы (необязательно, но заметим)

- DTO для передачи данных в сервис
- Кеширование списка товаров через Redis с инвалидацией
- Event/Listener вместо прямого диспатча джобы
- Кастомная таблица `order_exports` для отслеживания статуса экспорта
- любая OpenAPI/Swagger-документация
