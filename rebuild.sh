#!/bin/bash
set -e

IMAGE_NAME="forno-backend"
CONTAINER_NAME="forno-backend"
PORT="8000:8000"
NETWORK_NAME="forno-network"

if [ "$(docker ps -aq -f name=^${CONTAINER_NAME}$)" ]; then
    echo "Removendo container ${CONTAINER_NAME}..."
    docker rm -f "${CONTAINER_NAME}"
fi

if [ "$(docker images -q ${IMAGE_NAME})" ]; then
    echo "Removendo imagem ${IMAGE_NAME}..."
    docker rmi -f "${IMAGE_NAME}"
fi

docker network inspect "${NETWORK_NAME}" >/dev/null 2>&1 \
  || docker network create "${NETWORK_NAME}"

echo "Construindo imagem ${IMAGE_NAME}..."
docker build -t "${IMAGE_NAME}" .

echo "Iniciando container ${CONTAINER_NAME}..."

docker run -d --name "${CONTAINER_NAME}" \
  --network "${NETWORK_NAME}" \
  -p "${PORT}" "${IMAGE_NAME}"

echo "Rodando php artisan config:clear"
docker exec -it "${CONTAINER_NAME}" php artisan config:clear

echo "Container ${CONTAINER_NAME} rodando na porta ${PORT}."
