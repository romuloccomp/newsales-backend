#!/bin/bash
set -e

IMAGE_NAME="forno-backend"
CONTAINER_NAME="forno-backend"
PORT="8000:8000"

if [ "$(docker ps -aq -f name=^${CONTAINER_NAME}$)" ]; then
    echo "Removendo container ${CONTAINER_NAME}..."
    docker rm -f "${CONTAINER_NAME}"
fi

if [ "$(docker images -q ${IMAGE_NAME})" ]; then
    echo "Removendo imagem ${IMAGE_NAME}..."
    docker rmi -f "${IMAGE_NAME}"
fi

echo "Construindo imagem ${IMAGE_NAME}..."
docker build -t "${IMAGE_NAME}" .

echo "Iniciando container ${CONTAINER_NAME}..."
docker run -d --name "${CONTAINER_NAME}" -p "${PORT}" "${IMAGE_NAME}"

docker exec -it "${CONTAINER_NAME}" php artisan config:clear

echo "Container ${CONTAINER_NAME} rodando na porta ${PORT}."
