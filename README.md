# Api de pagamentos

Esta é uma api que possui dois tipos de usuários:

* Usuários comuns
* Lojistas

Como regra temos que apenas os usuários comuns podem fazer transferências e os lojistas apenas podendo apenas receber transferências.

## Como utilizar

Após clonar o projeto, acesse a pasta raiz do projeto e siga as instruções abaixo:

* Criando arquivo contendo as configurações de ambiente da aplicação: `cp .env.example .env`

* No arquivo criado anteriormente altere as informações de conexão ao banco de dados que irá ser utilizado

* Instale as dependências do projeto: `composer install`

* Gere uma nova chave do ambiente: `php artisan key:generate`

* Gere uma nova chave do ambiente: `php artisan jwt:secret`

* Crie as tabelas no banco de dados: `php artisan migrate`

* Execute a aplicação: `php artisan serve`

Após cumprir todos esses passos a api estará rodando no endereço: `http://localhost:8000/api/`


## Documentação

A documentação da api foi gerada utilizando o swagger, após feito o processo de instalação do projeto, acesse:
`
http://localhost:8000/api/documentation
`


## Testes

Para executar os testes automatizados acesse a pasta raiz do projeto e execute `php artisan test`