FROM php:8.1-fpm

ARG user
ARG uid

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
	libxml2-dev \
	libzip-dev \
    zip \
    nano \
    git \
    curl \
    unzip \
    && docker-php-ext-install zip

# Install extensions for php
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod +x /home

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
	chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]

User $user