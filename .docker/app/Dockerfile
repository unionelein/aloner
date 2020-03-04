FROM phpdockerio/php74-fpm:latest

WORKDIR "/application"

RUN apt-get update && apt-get -y --no-install-recommends install \
    php7.4-mysql \
    php-xdebug \
    php-yaml \
    php-zmq \
    php-amqp

RUN apt-get update && apt-get -y --no-install-recommends install supervisor

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN apt-get update && apt-get -y --no-install-recommends install \
    nodejs \
    yarn

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN mkdir -p /var/log/supervisor

CMD /usr/bin/supervisord -c /etc/supervisor/supervisord.conf