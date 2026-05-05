# PHP 8.2 Apache base image use karein
FROM php:8.2-apache

# Database se connect karne ke liye zaroori extensions install karein
RUN docker-php-ext-install pdo pdo_mysql

# Apache ka rewrite module enable karein (taki URL routing theek se kaam kare)
RUN a2enmod rewrite

# Apne saare project files ko container ke html folder mein copy karein
COPY . /var/www/html/

# Render ke liye port 80 expose karein
EXPOSE 80
