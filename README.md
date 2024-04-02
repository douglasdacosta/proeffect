intalando ambiente docker

docker pull php:8.3.1-apache

docker pull php:7.2.34-apache

Depois de cloando entre no progeto e rode

Dentro do diretório
$cd laradock
$sudo docker-compose up -d nginx mysql phpmyadmin
$sudo docker-compose ps
$sudo docker-compose exec --user=laradock workspace bash
$php artisan migrate

Para logar no projeto utilize
Login: eplax@eplax.com
senha: eplax


DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=proeffect
DB_USERNAME=root
DB_PASSWORD=root
