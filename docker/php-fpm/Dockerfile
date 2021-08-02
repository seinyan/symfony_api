FROM phpdockerio/php73-fpm:latest
WORKDIR "/var/www/api"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.3-mysql \
    && apt-get -y --no-install-recommends install  php7.3-pgsql \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get -y install zip \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer

RUN apt-get update \
    && apt-get -y install openssh-server \
    && apt-get -y install sshpass

RUN apt-get update \
    && apt-get -y install ffmpeg \
