FROM php:8.2-apache
ENV ACCEPT_EULA=Y
RUN apt-get update && apt-get install -y gnupg2
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - 
RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list 
RUN apt-get update 
RUN ACCEPT_EULA=Y apt-get -y --no-install-recommends install msodbcsql17 unixodbc-dev 
RUN pecl channel-update pecl.php.net
RUN pecl install sqlsrv -v
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

COPY . /var/www/html/
