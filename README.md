## 📌 Sobre o Projeto

O **BarberTime** é uma plataforma web no modelo **SaaS (Software as a Service)** desenvolvida para otimizar o gerenciamento de agendamentos em barbearias.

O sistema substitui processos manuais por uma solução digital eficiente, proporcionando:

* Organização de horários
* Redução de conflitos de agenda
* Melhor experiência para clientes
* Maior controle para administradores e barbeiros

---

## 🎯 Objetivo

Desenvolver uma plataforma que permita:

* Clientes agendarem serviços de forma prática
* Barbeiros gerenciarem sua agenda
* Administradores controlarem toda a operação

---

## 🚀 Funcionalidades

### 👤 Cliente

* Cadastro e autenticação
* Visualização de horários disponíveis
* Agendamento de serviços
* Cancelamento e reagendamento
* Histórico de atendimentos

### 💈 Barbeiro

* Visualização da agenda diária
* Controle de atendimentos
* Organização de horários

### 🛠️ Administrador

* Gerenciamento de barbeiros
* Cadastro de serviços (preço e duração)
* Controle geral de agendamentos
* Bloqueio de horários

---

## 📋 Requisitos

### ✅ Funcionais

* RF01 - Cadastro de usuários
* RF02 - Autenticação
* RF03 - Gerenciamento de perfil
* RF04 - Cadastro de barbeiros
* RF05 - Cadastro de serviços
* RF06 - Visualização de horários
* RF07 - Agendamento
* RF08 - Cancelamento
* RF09 - Reagendamento
* RF10 - Painel administrativo
* RF11 - Histórico de agendamentos
* RF12 - Controle de agenda
* RF13 - Bloqueio de horários
* RF14 - Logout

### ⚙️ Não Funcionais

* Interface intuitiva
* Tempo de resposta ≤ 2 segundos
* Responsividade (mobile e desktop)
* Segurança (criptografia de senhas)
* Disponibilidade 24/7
* Escalabilidade
* Arquitetura MVC
* Compatibilidade com navegadores modernos

---

## 🏗️ Arquitetura

O sistema segue o padrão **MVC (Model-View-Controller)**:

* **Model:** regras de negócio e dados
* **View:** interface com o usuário
* **Controller:** intermediação entre Model e View

---

## 💻 Tecnologias Utilizadas

* **Backend:** PHP
* **Frontend:** HTML, CSS, JavaScript
* **Banco de Dados:** MySQL
* **Versionamento:** Git + GitHub

---

## 📊 Modelagem do Sistema

O projeto inclui:

* Diagrama de Casos de Uso
* Diagrama de Classes
* Diagrama Entidade-Relacionamento (DER)

---

# 📋 Casos de Uso e Histórias de Usuário - BarberTime

Este documento contém a especificação dos Casos de Uso e das Histórias de Usuário do projeto **BarberTime**, estruturados com base nos requisitos funcionais e stakeholders do sistema SaaS para agendamento de barbearias.

## 🎯 Casos de Uso (Use Cases)

Os casos de uso descrevem as interações principais entre os atores (**Cliente**, **Barbeiro** e **Administrador**) e o sistema.

| Ator Principal | Caso de Uso | Descrição | Requisitos (RF) |
| :--- | :--- | :--- | :--- |
| **Cliente** | Gerenciar Conta | Cadastrar, realizar login, atualizar perfil e fazer logout. | RF01, RF02, RF03, RF14 |
| **Cliente** | Realizar Agendamento | Visualizar horários, escolher barbeiro/serviço e confirmar o agendamento. | RF06, RF07 |
| **Cliente** | Modificar Agendamento | Cancelar ou reagendar um horário previamente marcado. | RF08, RF09 |
| **Cliente** | Consultar Histórico | Acessar a lista de serviços já realizados na barbearia. | RF11 |
| **Barbeiro** | Consultar Agenda | Visualizar os atendimentos marcados para o dia ou semana. | RF12 |
| **Barbeiro** | Gerenciar Disponibilidade | Bloquear horários em que não poderá realizar atendimentos. | RF13 |
| **Administrador** | Gerenciar Equipe | Cadastrar, editar ou remover barbeiros do sistema. | RF04 |
| **Administrador** | Gerenciar Serviços | Adicionar e atualizar os tipos de corte/serviço, preço e duração. | RF05 |
| **Administrador** | Painel de Controle | Visualizar e gerenciar todos os agendamentos e horários bloqueados da barbearia. | RF10, RF13 |

---

## 📖 Histórias de Usuário (User Stories)

As histórias de usuário seguem o formato ágil padrão: *Como um [ator], eu quero [ação] para que [valor/motivo]*.

| ID | Ator | Ação (Eu quero...) | Valor (Para que...) | RF |
| :--- | :--- | :--- | :--- | :--- |
| **US01** | Cliente | Me cadastrar com e-mail e senha | Eu possa acessar a plataforma e agendar serviços. | RF01 |
| **US02** | Cliente | Fazer login e gerenciar meu perfil | Eu mantenha meus dados atualizados e acesse minha conta com segurança. | RF02, RF03 |
| **US03** | Cliente | Visualizar os horários disponíveis | Eu saiba exatamente quando posso ser atendido. | RF06 |
| **US04** | Cliente | Agendar um serviço escolhendo data, hora e profissional | Eu garanta meu atendimento de forma rápida e sem ligar para o local. | RF07 |
| **US05** | Cliente | Cancelar ou reagendar meu horário | Eu possa adaptar minha agenda caso ocorra algum imprevisto. | RF08, RF09 |
| **US06** | Cliente | Ver meu histórico de agendamentos | Eu lembre quando foi meu último corte e com qual profissional. | RF11 |
| **US07** | Barbeiro | Visualizar minha agenda do dia | Eu possa me organizar para os atendimentos e otimizar meu tempo. | RF12 |
| **US08** | Barbeiro | Bloquear horários na minha agenda | Eu evite que clientes marquem horários no meu período de pausa ou almoço. | RF13 |
| **US09** | Administrador | Cadastrar e gerenciar barbeiros | Eu mantenha o quadro de funcionários da barbearia atualizado no sistema. | RF04 |
| **US10** | Administrador | Cadastrar serviços com preço e duração | Os clientes saibam exatamente o que estão contratando e o tempo necessário. | RF05 |
| **US11** | Administrador | Acessar um painel geral de agendamentos | Eu tenha o controle total da operação e do fluxo de clientes na barbearia. | RF10 |

## 🧠 Metodologia

O desenvolvimento segue o framework ágil **Scrum**, com:

* Sprints semanais
* Entregas incrementais
* Uso de:

  * Product Backlog
  * Sprint Backlog
  * Daily Scrum
  * Sprint Review
  * Retrospective

---

## ⚠️ Riscos do Projeto

* Atrasos no desenvolvimento
* Falta de integração da equipe
* Problemas técnicos
* Mudanças de requisitos
* Baixa experiência da equipe

---

## 👥 Equipe

* José Carlos Da Silva Neto
* José Guilherme Ferreira Sobrinho
* Matheus Felipe Lopes Valadares
* Mario Felipe Bastos Noleto

---

## 🎓 Instituição

**Universidade Federal do Tocantins (UFT)**

---

## 📅 Data

Março de 2026

---

## 📌 Conclusão

O **BarberTime** surge como uma solução moderna para digitalizar o processo de agendamento em barbearias, aumentando a eficiência operacional e melhorando a experiência do cliente.

Além do contexto acadêmico, o projeto possui potencial real de mercado, podendo evoluir para uma solução escalável e comercial.

---
