# Demo Crud service with Symfony 4

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

### /api/category

**create**
```
curl --location --request POST 'localhost:3000/api/category' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "first",
    "description": "first desc"
}'

```

**update**
```
curl --location --request PUT 'localhost:3000/api/category/1' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "first edit",
    "description": "desc edit"
}'
```

**delete**
```
curl --location --request DELETE 'localhost:3000/api/category/1' \
--header 'Content-Type: application/json' \
--data-raw '{}'
```

### /api/product
```
curl --location --request POST 'localhost:3000/api/product' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "seconds",
    "category": 2,
    "price": 2.34,
    "currency": "EUR"
}'
