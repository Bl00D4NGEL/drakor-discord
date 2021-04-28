# Docker Dev env

* PHP 8.0.0
* NGINX 1.19
* MARIADB 10.5

## init
1. download or clone [github](https://github.com/muchete-cgn/dev-docker.git)
2. setup your environment `make setup`
4. start the container `make start`
5. open [localhost:8080](http://localhost:8080)

## run cli
1. use `make console` to run CLI in php container.

## stop
1. run `make stop` to stop containers.

## webserver
configure pages using the [sites-enabled](.docker/nginx/sites-enabled) folder. By default, php is linked to the [app/](app/) folder. The webserver is listening to [localhost:8080](http://localhost:8080)

## permissions
1. php volume permissions are set to `1000:www-data`. Simply modify the [.env](.env) file to change default permissions

## inspired by
This repository was heavily inspired by [https://github.com/muchete-cgn/dev-docker](https://github.com/muchete-cgn/dev-docker)
