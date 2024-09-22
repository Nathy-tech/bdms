 # Use the official PHP 8.0 image with Apache
FROM php:8.0-apache

# Copy the current directory contents into the container
COPY . /var/www/html/

# Install any PHP extensions you need
RUN docker-php-ext-install pdo pdo_mysql

# Expose port 80
EXPOSE 80
