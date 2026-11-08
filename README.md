# Shopping Cart REST API

A RESTful :) (Hello Thorsten) shopping cart API built with PHP 8.3 and Symfony 7.4. No API Platform, as required :)

## Requirements

- PHP 8.3
- Composer
- SQLite (`php8.3-sqlite3`)

## Installation

```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

Or using the Makefile:

```bash
make install
```

## Running the application

```bash
make run
```

The API will be available at `http://localhost:8000`.

## Seeded products

The fixture loads 8 electronics products:

| SKU      | Name                                 | Price     |
|----------|--------------------------------------|-----------|
| AGAN0001 | Smartphone Pro 15                    | €999.99   |
| AGAN0002 | Smartphone Lite 15                   | €599.99   |
| AGAN0003 | Ultrabook 14" 16GB                   | €1299.99  |
| AGAN0004 | Gaming Laptop 15" RTX                | €1799.99  |
| AGAN0005 | Wireless Noise-Cancelling Headphones | €249.99   |
| AGAN0006 | True Wireless Earbuds                | €129.99   |
| AGAN0007 | Smartwatch Series 9                  | €399.99   |
| AGAN0008 | Tablet 11" 256GB                     | €749.99   |

## API Endpoints

### Create a cart
```
POST /carts
```

### Show a cart
```
GET /carts/{cartId}
```

### Add an item to the cart
```
POST /carts/{cartId}/items
Content-Type: application/json

{
  "sku": "AGAN0001",
  "quantity": 2
}
```

### Update item quantity
```
PATCH /carts/{cartId}/items/{itemId}
Content-Type: application/json

{
  "quantity": 5
}
```

### Remove an item from the cart
```
DELETE /carts/{cartId}/items/{itemId}
```

## Running the tests

```bash
make test
```

## Postman

A Postman collection is included at `postman_collection.json`. Import it and run the requests top to bottom — cart ID and item ID are saved automatically between requests.
