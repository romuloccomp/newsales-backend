## Dockering app

### Build
docker network create api-network

docker build -t admin-forno .

### Run
docker run -d -p 8080:80 --name admin-forno --network api-network admin-forno

### Migrations

docker exec -it admin-forno php artisan migrate