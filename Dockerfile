FROM php:8.2-apache

# Enable mysqli extension
RUN docker-php-ext-install mysqli

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
