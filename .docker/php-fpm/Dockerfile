FROM phpdockerio/php73-fpm:latest
WORKDIR "/application"

RUN apt-get update && apt-get -y --no-install-recommends install \
    php7.3-mysql \
    php-xdebug \
    php-yaml \
    php-zmq \
    php-amqp

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN apt-get update && apt-get -y --no-install-recommends install \
    nodejs \
    yarn

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*