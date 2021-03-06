# Demo Crud service with Symfony 4

## How to run


```
$ cp .env.dist .env
```

***set your EXCHANGE_API_TOKEN env variable in the .env file***

***run docker compose***
```
docker compose up --build --abort-on-container-exit
```

__note__
## Architecture

### Entities

**Category**
- id
- name
- description

**Product**
- id
- name
- category_id
- price
- currency
- featured

## Endpoints

### /api/categories

**create**
```
curl --location --request POST 'localhost:3000/api/categories' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "first",
    "description": "first desc"
}'

```

**update**
```
curl --location --request PUT 'localhost:3000/api/categories/1' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "first edit",
    "description": "desc edit"
}'
```

**delete**
```
curl --location --request DELETE 'localhost:3000/api/categories/1' \
--header 'Content-Type: application/json' \
--data-raw '{}'
```

### /api/products

**create**
```
curl --location --request POST 'localhost:3000/api/products' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "seconds",
    "category": 2,
    "price": 2.34,
    "currency": "EUR"
}'
```

**all**
```
curl --location --request GET 'localhost:3000/api/products' \
--header 'Content-Type: application/json' \
--data-raw '{}'
```

**featured**
```
curl --location --request GET 'localhost:3000/api/products/featured?currency=USD' \
--header 'Content-Type: application/json' \
--data-raw '{}'
```