FROM php:8.3-apache

# Extensão para o PHP conversar com o MySQL via PDO
RUN docker-php-ext-install pdo_mysql

# Não exibir a versão do PHP nos cabeçalhos HTTP
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/lab.ini
