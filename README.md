## Dockering app

### Build

```bash
docker network create api-network
```

```bash
docker build -t admin-forno .
```
### Run

```bash
docker run -d -p 8080:80 --name forno-backend --network forno-network forno-backend
```
### Migrations

```bash
docker exec -it forno-backend php artisan migrate
```
