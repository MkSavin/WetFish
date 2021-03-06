FROM nginx

RUN apt-get update && \
    apt-get install -y nano

ADD docker/nginx/vconf.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/laravel
