# Event Manager

Bem-vindo ao **Event Manager**, uma aplicação para gerenciar eventos de forma simples e eficiente. Este repositório contém a implementação backend do sistema, utilizando PHP e MySQL como base de dados.

---

## 🚀 Começando

Siga os passos abaixo para configurar e iniciar o projeto localmente.

### 🛠️ Requisitos
- PHP 7.4 ou superior
- Composer instalado
- MySQL instalado e configurado

---

## 📂 Estrutura da Base de Dados

A base de dados utilizada no projeto é **MySQL**. O arquivo SQL para criação da estrutura da base está localizado em:

```plaintext
database/event_manager_db.sql

---

## ⚙️ Configuração do Ambiente

Copie o arquivo .env_example para .env:

- cp .env_example .env

Atualize o arquivo .env com suas configurações locais, como:
Credenciais do banco de dados
Configurações específicas do ambiente

---

## 🧑‍💻 Instalação de Dependências

O projeto utiliza o Composer para gerenciar dependências. Para instalar as dependências, execute o seguinte comando:

- composer install

---

## ▶️ Inicializando o Projeto

Para rodar o projeto localmente, execute o comando abaixo:

- php -S localhost:7000 -t public
Após isso, acesse o projeto no navegador em:

http://localhost:7000