# Como executar o projeto BarberTime

Este documento descreve os passos necessários para executar o projeto **BarberTime** em ambiente local utilizando **XAMPP**, **Apache**, **PHP** e **MariaDB/MySQL**.

---

## 1. Instalar as dependências

Instale o **XAMPP** para execução do servidor Apache.

Também é necessário ter o **MariaDB** instalado e em execução.

> Observação: neste projeto, não é obrigatório utilizar o banco de dados que vem junto com o XAMPP. Caso utilize uma instalação separada do MariaDB, confira se o serviço está ativo e se a porta utilizada está correta.

---

## 2. Clonar o repositório

Clone o repositório dentro da pasta `htdocs` do XAMPP:

```txt
C:\xampp\htdocs
```

Exemplo:

```bash
cd C:\xampp\htdocs
git clone URL_DO_REPOSITORIO barbearia_SaaS
```

A estrutura esperada do projeto é:

```txt
C:\xampp\htdocs\barbearia_SaaS
```

---

## 3. Criar o banco de dados

Acesse o MariaDB/MySQL pelo phpMyAdmin, terminal ou outra ferramenta de gerenciamento de banco de dados e crie o banco:

```sql
CREATE DATABASE barbertime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Depois selecione o banco:

```sql
USE barbertime;
```

---

## 4. Importar os scripts SQL

Execute primeiro o script responsável pela criação das tabelas:

```txt
database/schema.sql
```

Depois execute o script responsável pelas inserções iniciais:

```txt
database/seed.sql
```

A ordem correta é:

```txt
1. schema.sql
2. seed.sql
```

O arquivo `schema.sql` cria a estrutura do banco de dados, incluindo as tabelas, chaves primárias e chaves estrangeiras.

O arquivo `seed.sql` insere alguns dados iniciais do sistema, como:

- administrador;
- cliente de teste;
- barbeiros;
- serviços;
- vínculos entre barbeiros e serviços.

> Observação: o script `seed.sql` não insere agendamentos iniciais. Os agendamentos devem ser criados pela interface do sistema.

---

## 5. Configurar a conexão com o banco

Abra o arquivo de configuração do banco de dados:

```txt
config/database.php
```

Configure os dados de conexão conforme o MariaDB da sua máquina.

Exemplo:

```php
$host = 'localhost';
$dbname = 'barbertime';
$user = 'root';
$password = 'SUA_SENHA';
```

Caso seu MariaDB utilize outra porta, como `3307`, ajuste a conexão se necessário.

Exemplo:

```php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'barbertime';
$user = 'root';
$password = 'SUA_SENHA';
```

Verifique se os dados do arquivo `config/database.php` estão de acordo com:

- host do banco;
- porta do banco;
- nome do banco;
- usuário;
- senha.

---

## 6. Iniciar os serviços

Abra o painel do XAMPP e inicie o serviço:

```txt
Apache
```

O MariaDB também deve estar em execução, seja pelo próprio XAMPP ou por uma instalação separada.

---

## 7. Acessar o sistema

No navegador, acesse:

```txt
http://localhost/barbearia_SaaS/public/index.php
```

Ou diretamente pela tela de login:

```txt
http://localhost/barbearia_SaaS/public/index.php?action=login
```

---

## 8. Contas iniciais

O arquivo `seed.sql` possui os dados iniciais do sistema.

Confira as credenciais exatas no próprio `seed.sql`.

Exemplo de contas de teste:

```txt
Admin:
E-mail: admin@barbertime.com
Senha: admin123

Cliente:
E-mail: cliente@teste.com
Senha: cliente123

Barbeiro:
E-mail: josebarbeiro@gmail.com
Senha: barbeiro123
```

A conta de administrador é importante para acessar o painel administrativo e realizar cadastros pela interface do sistema.

---

## 9. Rotas principais do sistema

Algumas rotas úteis para teste:

```txt
Login do cliente:
http://localhost/barbearia_SaaS/public/index.php?action=login

Login do barbeiro:
http://localhost/barbearia_SaaS/public/index.php?action=barber_login

Login do administrador:
http://localhost/barbearia_SaaS/public/index.php?action=admin_login

Home do cliente:
http://localhost/barbearia_SaaS/public/index.php?action=home

Histórico de agendamentos:
http://localhost/barbearia_SaaS/public/index.php?action=scheduling_history
```

---

## 10. Possíveis problemas

### Erro de conexão com o banco

Verifique se:

- o MariaDB está ligado;
- o banco `barbertime` foi criado;
- os scripts `schema.sql` e `seed.sql` foram executados na ordem correta;
- o usuário e a senha no `config/database.php` estão corretos;
- a porta do MariaDB está correta;
- a extensão `pdo_mysql` está habilitada no PHP.

### Página não carrega CSS atualizado

Se alterações visuais não aparecerem no navegador, pode ser cache.

Tente atualizar com:

```txt
Ctrl + F5
```

Ou adicione uma versão no link do CSS:

```html
<link rel="stylesheet" href="/barbearia_SaaS/app/css/arquivo.css?v=2">
```

### Projeto em pasta com nome diferente

Se o projeto não estiver exatamente em:

```txt
C:\xampp\htdocs\barbearia_SaaS
```

os links absolutos usados no CSS e nas views podem precisar de ajuste.

Exemplo:

```html
<link rel="stylesheet" href="/barbearia_SaaS/app/css/login.css">
```

Caso a pasta tenha outro nome, substitua `barbearia_SaaS` pelo nome correto.

---

## 11. Ordem resumida de execução

```txt
1. Instalar XAMPP e MariaDB
2. Clonar o projeto em C:\xampp\htdocs\barbearia_SaaS
3. Criar o banco barbertime
4. Executar database/schema.sql
5. Executar database/seed.sql
6. Configurar config/database.php
7. Iniciar Apache
8. Garantir que o MariaDB esteja rodando
9. Acessar http://localhost/barbearia_SaaS/public/index.php
```
