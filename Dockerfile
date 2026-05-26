FROM php:8.4-cli

# Instalação de dependências do sistema e extensões necessárias para o Laravel 12
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos da aplicação
COPY . .

# Permissões do Laravel para storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instala as dependências PHP da aplicação
RUN composer install --optimize-autoloader --no-dev

# Expõe a porta que será utilizada pelo servidor PHP
EXPOSE 8000

# Script ou comando de inicialização
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
