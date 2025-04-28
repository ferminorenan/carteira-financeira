Documentação do Projeto CodeIgniter 4
=====================================

Este documento descreve a estrutura, configuração e funcionalidades do projeto CodeIgniter 4, focado em um sistema básico de autenticação e gestão de transações financeiras.

# 1\. Visão Geral
---------------

Este projeto é uma aplicação web construída com o framework CodeIgniter 4. Ele fornece funcionalidades essenciais para:

*   Autenticação de usuários (Registro e Login).
*   Um dashboard para usuários logados.
*   Gestão de transações financeiras (Depósito, Transferência, Histórico e Reversão de Transações).

A aplicação utiliza um filtro de autenticação para proteger rotas que exigem que o usuário esteja logado. As operações de transação são realizadas através de endpoints que retornam respostas JSON.

# 2\. Requisitos do Sistema
-------------------------

Para rodar este projeto, você precisará ter instalado:

*   **PHP:** Versão 8.1 ou superior (conforme `composer.json`).
*   **Composer:** Para gerenciar as dependências do PHP.
*   **Servidor Web:** Apache, Nginx ou o servidor de desenvolvimento embutido do PHP.
*   **Banco de Dados:** PostgreSQL (as migrations utilizam tipos específicos de PostgreSQL como `SERIAL` e `ENUM`). Se estiver usando outro BD (MySQL, SQLite, etc.), as migrations precisarão ser adaptadas.

# 3\. Configuração do Ambiente
----------------------------

Siga os passos abaixo para configurar o projeto localmente:

## 1.  **Clone o Repositório:**
    
    Bash
      git clone <url_do_seu_repositorio>
      cd <nome_do_seu_projeto>
    
## 2.  **Instale as Dependências:** 
Certifique-se de ter o Composer instalado e rodando.
    
    Bash
      composer install

Isso instalará o CodeIgniter 4 framework e outras bibliotecas listadas no `composer.json`.
    
## 3.  **Configure o Arquivo `.env`:** 
Copie o arquivo `env` para `.env` na raiz do projeto.
    
    Bash
      cp env .env
    
Edite o arquivo `.env` para configurar as informações do seu banco de dados e outras configurações ambientais. As linhas principais a serem modificadas são:
    
    Snippet de código
      #--------------------------------------------------------------------
      #DATABASE
      #--------------------------------------------------------------------
      #database.default.hostname = localhost # Ou o host do seu BD
      #database.default.database = nome_do_seu_banco # Nome do seu banco de  dados
      #database.default.username = seu_usuario # Usuário do banco de dados
      #database.default.password = sua_senha # Senha do banco de dados
      #database.default.DBDriver = PostgreSQL # Verifique se o driver está  correto para PostgreSQL
      #database.default.DBPrefix =
      #database.default.pConnect = false
      #database.default.DBDebug = true
      #database.default.charset = utf8
      #database.default.DBCollat = utf8_general_ci
      #database.default.swapPre = false
      #database.default.encrypt = false
      #database.default.compress = false
      #database.default.strictOn = false
      #database.default.failover = []
      #database.default.port = 5432 # Porta padrão do PostgreSQL (ajuste se  necessário)
      #--------------------------------------------------------------------
      # BASE URL
      #--------------------------------------------------------------------
      #app.baseURL = '' # Descomente e defina a URL base se necessário
    
    Certifique-se também de definir o ambiente de desenvolvimento:
      CI_ENVIRONMENT = development
    
## 4.  **Execute as Migrations do Banco de Dados:** 
As migrations criarão as tabelas `users` e `transactions` no seu banco de dados.
    
    Bash
      php spark migrate
    
**Nota:** As migrations fornecidas (`CreateUsersTable`, `CreateTransactionsTable`) contêm sintaxe específica para **PostgreSQL**, incluindo a criação de tipos `ENUM`. Se você estiver usando MySQL ou outro banco, precisará adaptar esses arquivos de migration para usar os tipos de dados e sintaxe apropriados (ex: usar `VARCHAR` e `CHECK` constraints no lugar de `ENUM`).

## 5.  **Execute a Aplicação:** 
Você pode usar o servidor de desenvolvimento embutido do CodeIgniter:
    
    Bash
      php spark serve
    
Ou configure um Virtual Host no seu servidor web (Apache/Nginx) apontando para a pasta `public/` do projeto.
    

# 4\. Estrutura do Projeto
------------------------

Este projeto segue a estrutura padrão do CodeIgniter 4 App Starter, com algumas adições nos diretórios `app/Controllers`, `app/Database/Migrations` e `app/Filters`.

*   **`app/Controllers/`**: Contém a lógica da aplicação.
    *   `BaseController.php`: Controlador base estendido pelos outros controladores.
    *   `AuthController.php`: Lida com as rotas e lógica de registro e login de usuários.
    *   `DashboardController.php`: Lida com a rota principal (`/`) após o login, mostrando o saldo e o histórico de transações.
    *   `TransactionController.php`: Lida com as operações de transação (depósito, transferência, histórico API, reversão).
*   **`app/Database/Migrations/`**: Contém os arquivos para criar e modificar a estrutura do banco de dados.
    *   `YYYY-MM-DD-HHMMSS_CreateUsersTable.php`: Cria a tabela `users`.
    *   `YYYY-MM-DD-HHMMSS_CreateTransactionsTable.php`: Cria a tabela `transactions`.
*   **`app/Filters/`**: Contém os filtros de requisição.
    *   `AuthFilter.php`: Verifica se o usuário está autenticado antes de permitir o acesso a certas rotas.
*   **`app/Models/`**: Deverá conter os modelos para interagir com as tabelas do banco de dados (`UserModel.php` e `TransactionModel.php`). Embora o código dos Models não tenha sido fornecido, eles são utilizados pelos controllers.
*   **`app/Config/Routes.php`**: Define todas as rotas da aplicação e as associa aos métodos dos controladores.
*   **`app/Views/`**: Deverá conter os arquivos de view (HTML com PHP) para renderizar as páginas (ex: `auth/register.php`, `auth/login.php`, `dashboard/index.php`).
*   **`public/`**: Diretório acessível publicamente, contém o `index.php` e arquivos estáticos (CSS, JS, imagens).

# 5\. Banco de Dados
------------------

O banco de dados possui duas tabelas principais, criadas pelas migrations:

## Tabela `users`

Armazena as informações dos usuários.

|Coluna|Tipo|Restrições/Notas|
|---|---|---|
|`id`|`INT`/`SERIAL`|Chave primária, auto-incremento, unsigned (PostgreSQL)|
|`name`|`VARCHAR(100)`|Nome completo do usuário|
|`email`|`VARCHAR(100)`|Email do usuário, deve ser único|
|`password`|`VARCHAR(255)`|Hash da senha do usuário|
|`balance`|`DECIMAL(10,2)`|Saldo atual do usuário, padrão 0.00|
|`created_at`|`DATETIME`|Timestamp de criação do registro|
|`updated_at`|`DATETIME`|Timestamp da última atualização|

## Tabela `transactions`

Armazena o histórico de transações financeiras. **Nota:** A migration fornecida utiliza tipos `ENUM` específicos de PostgreSQL.

|Coluna|Tipo|Restrições/Notas|
|---|---|---|
|`id`|`SERIAL`|Chave primária, auto-incremento (PostgreSQL)|
|`sender_id`|`INT`|ID do remetente (NULL para depósitos). Chave estrangeira para `users.id`.|
|`receiver_id`|`INT`|ID do destinatário. Chave estrangeira para `users.id`.|
|`type`|`transaction_type` (`ENUM` em PostgreSQL)|Tipo da transação: `'deposit'`, `'transfer'`, `'reversal'`.|
|`amount`|`DECIMAL(10,2)`|Valor da transação.|
|`status`|`transaction_status` (`ENUM` em PostgreSQL)|Status da transação: `'completed'`, `'pending'`, `'failed'`, `'reversed_by_receiver'`, `'reversed_by_sender'`. Padrão 'pending'.|
|`reference_transaction_id`|`INT`|ID da transação original que foi revertida (NULL para transações não revertidas). Chave estrangeira para `transactions.id`.|
|`created_at`|`TIMESTAMP`|Timestamp de criação da transação.|
|`updated_at`|`TIMESTAMP`|Timestamp da última atualização da transação.|

**Relações:**

*   `transactions.sender_id` -> `users.id` (ON DELETE SET NULL, ON UPDATE CASCADE)
*   `transactions.receiver_id` -> `users.id` (ON DELETE CASCADE, ON UPDATE CASCADE)
*   `transactions.reference_transaction_id` -> `transactions.id` (ON DELETE SET NULL, ON UPDATE CASCADE)

# 6\. Rotas e Endpoints
---------------------

O arquivo `app/Config/Routes.php` define as seguintes rotas:

|Método|URI|Controlador::Método|Descrição|Filtros|
|---|---|---|---|---|
|`GET`|`/register`|`AuthController::registerForm`|Exibe o formulário de registro.|Nenhum|
|`POST`|`/register`|`AuthController::register`|Processa os dados do formulário de registro.|Nenhum|
|`GET`|`/login`|`AuthController::loginForm`|Exibe o formulário de login.|Nenhum|
|`POST`|`/login`|`AuthController::login`|Processa os dados do formulário de login.|Nenhum|
|`GET`|`/logout`|`AuthController::logout`|Desconecta o usuário.|Nenhum|
|`GET`|`/`|`DashboardController::index`|Dashboard do usuário, mostra saldo e histórico.|`auth`|
|`POST`|`/transaction/deposit`|`TransactionController::deposit`|Realiza uma operação de depósito.|`auth`|
|`POST`|`/transaction/transfer`|`TransactionController::transfer`|Realiza uma operação de transferência.|`auth`|
|`GET`|`/transaction/reverse/(:num)`|`TransactionController::reverse/$1`|Reverte uma transação específica pelo ID.|`auth`|
|`GET`|`/transaction/history`|`TransactionController::history`|Retorna o histórico de transações em JSON.|`auth`|

## Detalhes dos Endpoints Protegidos (`auth` Filter):

**GET /**

*   **Descrição:** Página principal após o login, exibindo o saldo atual do usuário e uma lista formatada de suas transações recentes.
*   **Resposta (View):** Renderiza a view `dashboard/index`, passando dados do usuário logado e transações.

**POST /transaction/deposit**

*   **Descrição:** Realiza um depósito na conta do usuário logado.
*   **Parâmetros (POST):**
    *   `amount` (numeric, required): O valor a ser depositado. Deve ser maior que 0.
*   **Resposta (JSON):**
    *   Sucesso (200 OK): `{ "message": "Depósito realizado com sucesso." }`
    *   Erro de Validação (400 Bad Request): `{ "messages": { "amount": "Mensagem de erro de validação" } }`
    *   Erro no Servidor (500 Internal Server Error): `{ "status": 500, "message": "Erro ao realizar depósito." }`

**POST /transaction/transfer**

*   **Descrição:** Realiza uma transferência do usuário logado para outro usuário.
*   **Parâmetros (POST):**
    *   `receiver_id` (numeric, required): O ID do usuário destinatário.
    *   `amount` (numeric, required): O valor a ser transferido. Deve ser maior que 0.
*   **Resposta (JSON):**
    *   Sucesso (200 OK): `{ "message": "Transferência realizada com sucesso." }`
    *   Erro de Validação (400 Bad Request): `{ "messages": { "receiver_id": "...", "amount": "..." } }`
    *   Erro de Negócio (400 Bad Request): `{ "status": 400, "message": "Remetente e destinatário não podem ser o mesmo." }`, `{ "status": 400, "message": "Saldo insuficiente." }`, `{ "status": 400, "message": "Remetente não encontrado." }`, `{ "status": 400, "message": "Destinatário não encontrado." }`
    *   Erro no Servidor (500 Internal Server Error): `{ "status": 500, "message": "Erro ao realizar transferência." }`

**GET /transaction/reverse/(:num)**

*   **Descrição:** Tenta reverter uma transação específica pelo seu ID. O usuário logado deve ser o remetente ou o destinatário da transação para poder revertê-la.
*   **Parâmetros (URL Segment):**
    *   `:num`: O ID da transação a ser revertida.
*   **Resposta (JSON):**
    *   Sucesso (200 OK): `{ "message": "Transação revertida com sucesso." }`
    *   Não Encontrado (404 Not Found): `{ "status": 404, "message": "Transação não encontrada." }`
    *   Erro de Negócio (400 Bad Request): `{ "status": 400, "message": "Transação não pode ser revertida." }`
    *   Proibido (403 Forbidden): `{ "status": 403, "message": "Você não tem permissão para reverter esta transação." }`
    *   Erro no Servidor (500 Internal Server Error): `{ "status": 500, "message": "Erro ao reverter transação." }`

**GET /transaction/history**

*   **Descrição:** Retorna o histórico completo de transações do usuário logado em formato JSON.
*   **Resposta (JSON):**
    *   Sucesso (200 OK): `{ "transactions": [ { ... }, { ... } ] }`. Cada objeto de transação contém todos os campos da tabela `transactions` e um campo `can_reverse` (boolean) indicando se a transação está completa e pode ser revertida pelo usuário logado.
    *   Erro no Servidor (500 Internal Server Error): Retornará uma view de dashboard com um erro se houver falha na busca ou serialização das transações (conforme implementação no `DashboardController`). A implementação no `TransactionController::history` simplesmente retorna um array vazio ou o erro de banco se ocorrer.

# 7\. Autenticação e Autorização
------------------------------

O projeto implementa um sistema de autenticação baseado em sessão:

*   **Registro:** Novos usuários podem se registrar através das rotas `/register` (GET e POST). A senha é hasheada antes de ser salva no banco de dados (implícito no `UserModel` via callback `BeforeInsert`).
*   **Login:** Usuários existentes podem fazer login através das rotas `/login` (GET e POST). A senha fornecida é verificada contra o hash armazenado no banco de dados usando `password_verify()`. Em caso de sucesso, dados do usuário são armazenados na sessão (`user_id`, `user_name`, `user_email`, `isLoggedIn`).
*   **Logout:** A rota `/logout` destrói a sessão do usuário, desconectando-o.
*   **Filtro `AuthFilter`:** Este filtro é aplicado às rotas definidas com a opção `['filter' => 'auth']`. Ele verifica se a variável de sessão `isLoggedIn` é verdadeira. Se não for, o usuário é redirecionado para a página de login com uma mensagem de erro.

# 8\. Testes
----------

O projeto inclui dependências de desenvolvimento para testes (`fakerphp/faker`, `mikey179/vfsstream`, `phpunit/phpunit`) e um script `test` no `composer.json`.

Para executar os testes, utilize o seguinte comando:

    Bash
      composer test

Ou diretamente com o spark:

    Bash
      php spark test

(Nota: O código dos testes em si não foi fornecido, mas a estrutura está preparada para rodá-los).

# 9\. Licença
-----------

Este projeto é distribuído sob a licença MIT (conforme `composer.json`). Veja o arquivo `LICENSE` (não fornecido aqui, mas implícito na licença do App Starter) para mais detalhes.

* * *

# **Considerações Adicionais**

*   **Views:** A documentação assume que existem views correspondentes (`app/Views/auth/register.php`, `app/Views/auth/login.php`, `app/Views/dashboard/index.php`) para renderizar as interfaces de usuário.
*   **Models:** A funcionalidade dos controladores depende dos Models (`UserModel` e `TransactionModel`) que interagem com o banco de dados. Assuma que esses Models implementam métodos como `find()`, `where()`, `orderBy()`, `findAll()`, `insert()`, `update()`, `save()`, e possivelmente callbacks como `beforeInsert` no `UserModel` para hashing de senha.
*   **Tratamento de Erros:** A documentação menciona os tipos de respostas de erro baseadas no `ResponseTrait` usado no `TransactionController`. Certifique-se de que o tratamento de erros e exceções na sua aplicação seja robusto.
*   **Segurança:** A autenticação baseada em sessão é um bom começo, mas para aplicações mais complexas, considere medidas de segurança adicionais (CSRF protection, sanitização de inputs, etc.). O CodeIgniter fornece ferramentas para isso.

Esta documentação cobre os principais aspectos do seu projeto com base nos arquivos fornecidos. Você pode expandi-la adicionando seções sobre a instalação de dependências específicas, a estrutura das views, detalhes sobre os Models, e qualquer outra informação relevante para quem for utilizar ou desenvolver o projeto.