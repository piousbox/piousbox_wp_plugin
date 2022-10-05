##
## syntax=docker/dockerfile:1
##
## This builds a php image.
## For testing - in production this runs from "docker" repo.
##

FROM debian:bullseye

RUN apt-get update && \
  apt-get install -y \
    # apache2 \
    curl \
    default-mysql-client \
    git gnupg2 \
    imagemagick iputils-ping \
    lsof \
    net-tools \
    silversearcher-ag subversion \
    telnet \
    vim \
    wget \
    unzip

RUN echo "deb https://packages.sury.org/php/ bullseye main" > /etc/apt/sources.list.d/sury-php.list && \
  wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add - && \
  apt-get update

RUN apt-get install -y \
  php7.4 php-imagick php7.4-imagick \
  php7.4-cli php7.4-common php7.4-curl php7.4-intl \
  php7.4-mysql php7.4-xml php7.4-zip \
  php-xml php-mbstring php7.4-mbstring \
  php8.1-mysql

RUN touch /root/.bashrc && \
  echo "alias ll='ls -lah '" >> /root/.bashrc && \
  mkdir -p /opt/projects/php/piousbox_wp_plugin/bin

WORKDIR /opt/projects/php/piousbox_wp_plugin
ADD . /opt/projects/php/piousbox_wp_plugin

# CMD apachectl -D FOREGROUND

