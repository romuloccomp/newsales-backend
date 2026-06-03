#!/bin/bash

docker network create forno-network

docker build -t forno-backend .

docker run -d \
  --name forno-api \
  --network forno-network \
  -p 8000:8000 \
  forno-backend \
  php artisan serve --host=0.0.0.0 --port=8000

docker run -d \
  --name forno-worker \
  --network forno-network \
  forno-backend \
  /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
