
# API Restful - Pastelaria

## Sobre o Projeto

Esta API Restful foi desenvolvida para gerenciar as operações de Clientes, Produtos e Pedidos de uma pastelaria. O sistema suporta operações completas de CRUDL (Criar, Ler, Atualizar, Deletar e Listar) e inclui funcionalidades como envio de e-mails de confirmação de pedido e soft delete. Além disso, a API foi construída usando as melhores práticas do Laravel 11, com validação de dados, testes unitários e integração com Docker para facilitar o desenvolvimento e o deploy.

## Funcionalidades

- Clientes: Gerenciamento de informações de clientes, incluindo nome, e-mail, telefone, endereço completo e data de cadastro.
- Produtos: Gerenciamento de produtos com nome, preço e foto obrigatória.
- Pedidos: Criação de pedidos com a possibilidade de associar vários produtos a um cliente e envio de e-mail com os detalhes do pedido.
- Soft Delete: Implementado para os clientes, produtos e pedidos, permitindo restauração de registros deletados.

## Tecnologias Utilizadas

- PHP: 8.2
- Laravel: 11
- Docker: Para gerenciamento de contêineres.
- MySQL: Banco de dados relacional utilizado para armazenar os dados.
- Mail: Para envio de e-mails de confirmação dos pedidos.
- Composer: Gerenciador de dependências do PHP.
- PHPUnit: Para execução de testes unitários.

## Requisitos

- PHP: 8.2 ou superior
- Docker: Certifique-se de ter o Docker instalado e em execução na sua máquina.
- Composer: Para gerenciar as dependências da aplicação.

## Como Rodar a Aplicação

Siga as instruções abaixo para configurar e executar a aplicação localmente:

### 1. Clonar o Repositório

```bash
git clone https://github.com/diegodanciguer/pastelaria-api
cd pastelaria-api
```

### 2. Configurar o Arquivo `.env`

Copie o arquivo .env.example e renomeie para .env. Ajuste as variáveis de ambiente conforme necessário, incluindo as configurações de banco de dados e envio de e-mail.

```bash
cp .env.example .env
```

### 3. Build da Aplicação com Docker

Construa os contêineres necessários para rodar a aplicação:

```bash
docker-compose build
```

### 4. Iniciar os Contêineres

Inicie a aplicação com os contêineres do Docker:

```bash
docker-compose up -d
```

### 5. Instalar Dependências com Composer

Dentro do contêiner da aplicação, instale as dependências:

```bash
docker-compose exec app composer install
```

### 6. Executar Migrations e Seeders

Crie as tabelas no banco de dados e, se necessário, popule o banco de dados com dados iniciais:

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 7. Rodar os Testes Unitários

Garanta que a aplicação está funcionando corretamente executando os testes:

```bash
docker-compose exec app php artisan test
```

## Endpoints da API

### Clientes

| Método | Endpoint                      | Descrição                                   |
| ------- | ----------------------------- | --------------------------------------------- |
| GET     | `/api/clients`              | Listar todos os clientes.                     |
| POST    | `/api/clients`              | Criar um novo cliente.                        |
| GET     | `/api/clients/{id}`         | Exibir os detalhes de um cliente específico. |
| PUT     | `/api/clients/{id}`         | Atualizar os dados de um cliente.             |
| DELETE  | `/api/clients/{id}`         | Deletar um cliente (soft delete).             |
| POST    | `/api/clients/{id}/restore` | Restaurar um cliente deletado.                |

### Produtos

| Método | Endpoint                       | Descrição                                   |
| ------- | ------------------------------ | --------------------------------------------- |
| GET     | `/api/products`              | Listar todos os produtos.                     |
| POST    | `/api/products`              | Criar um novo produto.                        |
| GET     | `/api/products/{id}`         | Exibir os detalhes de um produto específico. |
| PUT     | `/api/products/{id}`         | Atualizar os dados de um produto.             |
| DELETE  | `/api/products/{id}`         | Deletar um produto (soft delete).             |
| POST    | `/api/products/{id}/restore` | Restaurar um produto deletado.                |

### Pedidos

| Método | Endpoint                     | Descrição                                        |
| ------- | ---------------------------- | -------------------------------------------------- |
| GET     | `/api/orders`              | Listar todos os pedidos.                           |
| POST    | `/api/orders`              | Criar um novo pedido para um cliente com produtos. |
| GET     | `/api/orders/{id}`         | Exibir os detalhes de um pedido específico.       |
| PUT     | `/api/orders/{id}`         | Atualizar um pedido.                               |
| DELETE  | `/api/orders/{id}`         | Deletar um pedido (soft delete).                   |
| POST    | `/api/orders/{id}/restore` | Restaurar um pedido deletado.                      |

### Observações

* Após a criação de um pedido, um e-mail de confirmação será enviado ao cliente com os detalhes do pedido.
* Utilize as rotas conforme descrito para operações CRUD nos módulos de Clientes, Produtos e Pedidos.

## Considerações Finais

Este projeto foi desenvolvido com o objetivo de criar uma API simples e eficiente para gerenciar uma pastelaria. Com uso de Docker, Laravel e práticas de testes, o sistema oferece robustez e facilidade para desenvolvimento e deploy.

## Autor

- [@diegodanciguer](https://www.github.com/diegodanciguer)
