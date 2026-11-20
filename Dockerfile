FROM php:8.3-apache

# розширення для роботи з MySQL
RUN docker-php-ext-install mysqli

# копіюємо файли застосунку
COPY . /var/www/html/

# HTTPS забезпечує сервер перед контейнером
ENV FORCE_HTTPS=0

# перевірка стану контейнера
HEALTHCHECK --interval=30s --timeout=5s CMD curl -f http://localhost/health.php || exit 1

EXPOSE 80
