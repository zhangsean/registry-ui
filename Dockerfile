FROM php:alpine

MAINTAINER Sean Zhang <zxf2342@qq.com>

WORKDIR /var/www/

EXPOSE     80

ENTRYPOINT ["php", "-S", "0.0.0.0:80"]

COPY . /var/www/
