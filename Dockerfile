# Image officielle PHP + Apache
FROM php:8.3-apache

# Active les modules Apache nécessaires
RUN a2enmod rewrite

# Copie tout le code
COPY . /var/www/html/

# Définit le dossier public comme racine web
WORKDIR /var/www/html
RUN echo "DocumentRoot /var/www/html/public" > /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf

# Expose le port
EXPOSE 80
