## Dockering app

### Build

```bash
docker network create forno-network
```

```bash
docker build -t forno-backend .
```

### Run

```bash
docker run -d -p 8000:8000 --name forno-backend --network forno-network forno-backend
```

### Migrations

```bash
docker exec -it forno-backend php artisan migrate
```

### Database container

```bash
docker run --name forno_db \
  --network forno-network \
  -e POSTGRES_USER=forno \
  -e POSTGRES_PASSWORD=F0rno@2026$ \
  -p 5432:5432 \
  -d postgres:17
```
