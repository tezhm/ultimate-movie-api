#!/bin/sh
set -e

docker pull mysql/mysql-server:5.6
docker run -p 3306:3306 --name mysqlserver -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=uma -e MYSQL_USER=apiserver -e MYSQL_PASSWORD=apiserver -d mysql/mysql-server:5.6
docker build -t apiserver -f docker/Dockerfile .
docker run -tid -p 80:80 --name apiserver --link mysqlserver:mysqldb apiserver
