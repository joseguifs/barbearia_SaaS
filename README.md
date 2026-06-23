# 💈 BarberTime

## 📘 Sobre o Projeto

O **BarberTime** é uma plataforma desenvolvida para facilitar o gerenciamento de barbearias, permitindo que clientes realizem agendamentos de maneira prática, enquanto barbeiros e administradores possuem ferramentas para controle e organização dos atendimentos.

Este repositório contém toda a documentação relacionada ao desenvolvimento, planejamento e estrutura do sistema.

---

# 📑 Índice

- [🎯 Objetivo](#-objetivo)
- [🚀 Benefícios da Plataforma](#-benefícios-da-plataforma)
- [📅 Planejamento das Sprints](#-planejamento-das-sprints)
- [📋 Requisitos do Sistema](#-requisitos-do-sistema)
- [📖 Casos de Uso e Histórias de Usuário](#-casos-de-uso-e-histórias-de-usuário)
- [🏗️ Entidades e Responsabilidades](#️-entidades-e-responsabilidades-do-sistema)
- [🏛️ Arquitetura](#️-arquitetura)
- [💻 Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [⚙️ Como Executar o Projeto](#️-como-executar-o-projeto)
- [🧠 Metodologia](#-metodologia)

---

# 🎯 Objetivo

Desenvolver uma plataforma capaz de atender clientes, barbeiros e administradores, oferecendo recursos para organização, controle e gerenciamento de agendamentos em barbearias.

## 👤 Clientes

- Realizar agendamentos de forma prática
- Consultar horários disponíveis
- Acompanhar histórico de atendimentos

## 💈 Barbeiros

- Organizar agendas de atendimento
- Visualizar compromissos diários
- Controlar atendimentos realizados

## 🛠️ Administradores

- Gerenciar barbeiros
- Cadastrar serviços
- Definir preços e duração dos serviços
- Controlar agendamentos do sistema

---

# 🚀 Benefícios da Plataforma

O BarberTime busca substituir processos manuais por uma solução digital eficiente, oferecendo:

- 📅 Organização inteligente de horários
- ⏱️ Redução de conflitos de agenda
- 💈 Melhor gerenciamento operacional
- 👥 Melhor experiência para clientes
- 📊 Maior controle administrativo
- 🌐 Facilidade de acesso pela web

---

# 📅 Planejamento das Sprints

Nesta seção estão documentados todos os processos relacionados às sprints do projeto, incluindo:

- Valor de cada sprint
- Funcionalidades desenvolvidas
- Responsáveis pelas tarefas
- Descrição das funcionalidades
- Revisão entre membros da equipe

## 📌 Sprints

- **Sprint 1** — [*Planejamento Sprint 1*](Markdowns/Sprints/Sprint1.md)
- **Sprint 2** — [*Planejamento Sprint 2*](Markdowns/Sprints/Sprint2.md)
- **Sprint 3** — [*Planejamento Sprint 3*](Markdowns/Sprints/Sprint3.md)
- **Sprint 4** — [*Planejamento Sprint 4*](Markdowns/Sprints/Sprint4.md)
- **Sprint 5** — [*Planejamento Sprint 5*](Markdowns/Sprints/Sprint5.md)

---

# 📋 Requisitos do Sistema

Nesta seção encontram-se todos os requisitos funcionais e informações relacionadas ao sistema BarberTime.

- [*Requisitos do Sistema*](Markdowns/RequirementsSystem/Requirements.md)

---

# 📖 Casos de Uso e Histórias de Usuário

Nesta seção estão documentados os casos de uso e as histórias de usuário do BarberTime.

O documento descreve as principais interações entre os atores do sistema — **Cliente**, **Barbeiro** e **Administrador** — e relaciona cada funcionalidade aos seus respectivos requisitos funcionais.

- [*Casos de Uso e Histórias de Usuário*](Markdowns/UserStoreAndCaseUse/UserStoreCaseUse.md)

---

# 🏗️ Entidades e Responsabilidades do Sistema

Aqui estão documentadas as principais entidades do BarberTime, juntamente com suas funcionalidades e responsabilidades dentro do sistema.

- [*Entidades do Sistema*](Markdowns/SystemEntity/Entitys.md)

---

# 🏛️ Arquitetura

O sistema segue o padrão arquitetural **MVC (Model-View-Controller)**.

## Estrutura MVC

- **Model:** responsável pelas regras de negócio, manipulação de dados e comunicação com o banco de dados.

- **View:** responsável pela interface visual e interação com o usuário.

- **Controller:** responsável por intermediar as requisições entre Model e View.

---

# 💻 Tecnologias Utilizadas

- **Backend:** PHP
- **Frontend:** HTML, CSS e JavaScript
- **Banco de Dados:** MariaDB
- **Versionamento:** Git e GitHub

---

# ⚙️ Como Executar o Projeto

As instruções completas para configurar e executar o BarberTime em ambiente local estão disponíveis no documento abaixo:

- [*Passo a passo para executar o projeto*](Markdowns/ExecuteProject/Instruction.md)

O documento contém orientações sobre:

- instalação e uso do XAMPP;
- configuração do Apache;
- configuração do MariaDB/MySQL;
- criação do banco de dados;
- execução dos scripts `schema.sql` e `seed.sql`;
- configuração do arquivo `config/database.php`;
- acesso às rotas principais do sistema;
- contas iniciais para teste;
- possíveis problemas durante a execução.

---

# 🧠 Metodologia

O desenvolvimento do BarberTime segue práticas baseadas em metodologias ágeis, utilizando sprints semanais e entregas incrementais.

## Práticas Utilizadas

- Product Backlog
- Sprint Backlog
- Daily Scrum
- Sprint Review

---
