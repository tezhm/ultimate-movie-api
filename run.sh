#!/bin/sh
set -e

echo "Stopping and removing running instances ..."
docker stop umaserver >/dev/null 2>/dev/null || true
docker rm umaserver >/dev/null 2>/dev/null || true
docker stop umamysql >/dev/null 2>/dev/null || true
docker rm umamysql >/dev/null 2>/dev/null || true

echo "Starting new instances ..."
docker pull mysql/mysql-server:5.6
docker run --name umamysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=uma -e MYSQL_USER=umaserver -e MYSQL_PASSWORD=umaserver -d mysql/mysql-server:5.6
docker build -t umaserver -f docker/Dockerfile .
docker run -tid -p 8000:80 --name umaserver --link umamysql:mysqldb umaserver
