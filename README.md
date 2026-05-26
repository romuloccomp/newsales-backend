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
docker run -d -p 8000:80 --name forno-backend --network forno-network forno-backend
```
### Migrations

```bash
docker exec -it forno-backend php artisan migrate
```
