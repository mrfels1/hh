# Используем официальный образ PHP с Apache
FROM php:7.4-apache

# Устанавливаем расширения PHP, включая pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Копируем исходный код приложения и файл composer.json в контейнер
COPY . /var/www/html

# Устанавливаем права доступа к директории с исходным кодом
RUN chown -R www-data:www-data /var/www/html

# Открываем порт 80
EXPOSE 80