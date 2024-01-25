# php8.2 apache2.4.52
FROM php:8.2-apache

# ミドルウェアのインストール
RUN apt-get update && apt-get install -y git vim \
    && docker-php-ext-install pdo_mysql

# # composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.jsをインストール
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs

# yarnをインストール
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install yarn

# apacheの設定
ADD ports.conf /etc/apache2/ports.conf
ADD a2ensite/* /etc/apache2/sites-available/
RUN mkdir -p /var/www/app/public \
    && a2enmod rewrite \
    && a2dissite 000-default.conf \
    && a2ensite laravel \
    && a2ensite schemaspy \
    && service apache2 restart