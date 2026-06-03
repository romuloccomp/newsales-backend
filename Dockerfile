FROM php:8.4-cli

# Instalação de dependências do sistema e extensões necessárias para o Laravel 12
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos da aplicação
COPY . .

# Copia o arquivo de configuração para o diretório padrão do supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Cria o diretório para os logs caso não exista
RUN mkdir -p /var/log/supervisor

# Permissões do Laravel para storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instala as dependências PHP da aplicação
RUN composer install --optimize-autoloader --no-dev

# COPY ./upservices.sh /usr/local/bin/

# RUN chmod +x /usr/local/bin/upservices.sh

# Expõe a porta que será utilizada pelo servidor PHP
EXPOSE 8000

# Script ou comando de inicialização
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
# CMD ["upservices.sh"]
