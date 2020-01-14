FROM phpdockerio/php73-fpm:latest
WORKDIR "/application"

RUN apt-get update && apt-get -y --no-install-recommends install \
    php7.3-mysql \
    php-xdebug \
    php-yaml \
    php-zmq \
    php-amqp

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*