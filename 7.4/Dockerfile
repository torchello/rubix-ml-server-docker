FROM php:7.4-cli

ENV SERVER_VERSION=dev-master
ENV TENSOR_VERSION=^2.0
ENV ML_VERSION=^0.2.4

ENV HOST=127.0.0.1
ENV PORT=8000
ENV MAX_CONCURRENT_REQUESTS=10
ENV SSE_RECONNECT_BUFFER=50
ENV MODEL_FILEPATH=data.model

ENV ACCESS_LOG_FILEPATH=access.log
ENV VERBOSE_LOG_FILEPATH=verbose.log

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions zip tensor

RUN install-php-extensions @composer-2

WORKDIR /var/www
COPY www .

RUN composer req rubix/server:$SERVER_VERSION rubix/tensor:$TENSOR_VERSION rubix/ml:$ML_VERSION --no-progress --optimize-autoloader

EXPOSE $PORT

CMD php server.php

