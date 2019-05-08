
## Teste Maxmilhas

Para executar o teste clone o projeto em localhost com o GIT

1-Instale o composer
2-Execute o comando composer-update
3-Renomear o env.example para .env e configurar o banco local
4-Criar o banco maxmilhas
5-Executar php artisan key:generate
6-Executar php artisan migrate
7-acessar o localhost/{pasta_onde_foi_clonado} (o index tem as funcionalidades solicitadas)
8-acessar o localhost/{pasta_onde_foi_clonado}/status (relatório de uso)

## Rotas para consulta via API Laravel

-Busca dados do CPF
GET localhost/{pasta_onde_foi_clonado}API/consulta/cpf?cpf=12312312312

-Adiciona novo CPF
POST localhost/{pasta_onde_foi_clonado}API/store
enviar variavel cpf : {valor}

-Deleta CPF
POST localhost/{pasta_onde_foi_clonado}API/destroy
enviar variavel cpf : {valor}

retorna os status da aplicação (uptime contagem requisicoes etc...)
GET localhost/{pasta_onde_foi_clonado}API/status

-Bloqueia o CPF
POST localhost/{pasta_onde_foi_clonado}API/block
enviar variavel cpf : {valor}

-Desloqueia o CPF
POST localhost/{pasta_onde_foi_clonado}API/unblock
enviar variavel cpf : {valor}

## Docker

Como foi a primeira vez que usei docker, não entendi como gerar o dockerfile
Copiei todo o projeto e zipei
Criei o projeto e funcionou local seguindo os passos abaixo

1-Descompacte o projeto
2-Acesse o terminal e abra {caminho www}/maxmilhas/laradock
3-Executar o comando docker-compose up -d mysql nginx
4-Excutar comando docker-compose exec --user=laradock workspace bash
5-Execute o comando composer-update
6-Renomear o env.example para .env e configurar o banco local
7-Criar o banco maxmilhas
8-Executar php artisan key:generate
9-Executar php artisan migrate
10-acessar o localhost (o index tem as funcionalidades solicitadas)
11-acessar o localhost/status (relatório de uso)

tive problemas com a ultima versão do mysql 8.0
e executei os comandos abaixo para resolver:

CREATE USER 'admin'@'localhost' IDENTIFIED WITH mysql_native_password BY '123456';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;
CREATE USER 'admin'@'%' IDENTIFIED WITH mysql_native_password BY '123456';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;
CREATE DATABASE IF NOT EXISTS `maxmilhas` COLLATE 'utf8_general_ci' ;
GRANT ALL ON `maxmilhas`.* TO 'admin'@'%' ;
FLUSH PRIVILEGES ;

alterar arquivo .env user admin password 123456

