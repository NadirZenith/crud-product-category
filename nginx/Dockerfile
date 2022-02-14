FROM nginx:1.17.6-alpine

# copy main server config
COPY conf.d/main.conf /etc/nginx/conf.d/default.conf

# make sure frontcontroller (app.php) exists
RUN mkdir -p /srv/web/ && touch /srv/web/app.php
