# Montink - Mini ERP em CodeIgniter 4

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Repositório: [https://github.com/alessandrodesign/montink](https://github.com/alessandrodesign/montink)

---

## Visão Geral

Mini ERP compacto desenvolvido em PHP com CodeIgniter 4, que oferece funcionalidades essenciais para gerenciamento de
produtos, estoque, vendas, cupons, carrinho de compras, autenticação, relatórios administrativos e integração via
webhooks.

---

## Tecnologias Utilizadas

- PHP 8.x
- CodeIgniter 4
- MySQL / MariaDB
- Bootstrap 5
- Composer
- ViaCEP API (consulta de CEP)

---

## Instalação

1. Clone o repositório:

   ```bash
   git clone https://github.com/alessandrodesign/montink.git
   cd montink
   ```

2. Instale as dependências:

   ```bash
   composer install
   ```

3. Configure o arquivo `.env` com suas credenciais de banco de dados e outras variáveis.

4. Execute as migrations:

   ```bash
   php spark migrate
   ```

5. (Opcional) Execute os seeders para dados iniciais:

   ```bash
   php spark db:seed DatabaseSeeder
   ```

6. Configure seu servidor web para apontar para a pasta `public/`.

---

## Funcionalidades Principais

### Autenticação

- Registro, login, logout e recuperação de senha via email com token seguro.

### Produtos e Estoque

- Cadastro de produtos com variações e controle de estoque.
- Status ativo/inativo para produtos.

### Carrinho e Pedidos

- Gerenciamento de carrinho em sessão.
- Cálculo de subtotal, descontos, frete (regras específicas).
- Finalização de pedidos com persistência.

### Cupons

- Criação e aplicação de cupons com regras de validade e desconto.

### Relatórios

- Relatórios de vendas diárias, vendas por produto, estoque e estoque baixo.

### Emails

- Envio de emails transacionais: boas-vindas, recuperação de senha, confirmação de pedido, status de pagamento.

### Webhooks

- Recebimento e processamento de notificações externas (ex: gateways de pagamento).

### Consulta de CEP

- Integração com ViaCEP para preenchimento automático de endereço no checkout.

---

## Estrutura do Projeto

```
app/
  Controllers/
  Entities/
  Enums/
  Helpers/
  Models/
  Services/
  Views/
public/
```

---

## Uso dos Helpers

- `auth()` — acesso ao serviço de autenticação.
- `cart()` — acesso ao serviço de carrinho.
- `money()` — formatação de valores monetários.

---

## Rotas Importantes

- `/auth/login` — login.
- `/auth/register` — registro.
- `/auth/forgot-password` — recuperação de senha.
- `/auth/reset-password/{token}` — redefinição de senha.
- `/cart` — visualização do carrinho.
- `/cart/add` — adicionar item ao carrinho.
- `/admin/sales-report` — relatório de vendas.
- `/admin/product-sales-report` — relatório de vendas por produto.
- `/admin/stock-report` — relatório de estoque.
- `/admin/low-stock-report` — relatório de estoque baixo.
- `/webhook/payment` — endpoint para webhooks de pagamento.

---

*Mini ERP - Simplificando a gestão do seu negócio.*