# PaperTech

Sistema web de gerenciamento de produtos e usuários, desenvolvido em PHP
e integrado a um banco de dados MySQL/MariaDB. O projeto também conta
com o **PaperBot**, um assistente que orienta os usuários sobre a
utilização das funcionalidades do sistema.

## Sumário

-   [Visão geral](#visão-geral)
-   [Funcionalidades](#funcionalidades)
-   [Tecnologias](#tecnologias)
-   [Estrutura do projeto](#estrutura-do-projeto)
-   [Requisitos](#requisitos)
-   [Instalação e configuração](#instalação-e-configuração)
-   [Configuração do PaperBot](#configuração-do-paperbot)
-   [Execução dos testes](#execução-dos-testes)
-   [Segurança e boas práticas](#segurança-e-boas-práticas)

## Visão geral

O PaperTech é uma aplicação web organizada em módulos. Seu objetivo é
oferecer uma interface para gerenciamento de produtos e usuários, com
autenticação e controle de acesso por tipo de usuário.

O sistema possui dois tipos de usuário:

-   **Administrador (`admin`):** possui acesso às funcionalidades
    administrativas, incluindo o gerenciamento de usuários.
-   **Funcionário (`funcionario`):** utiliza o sistema de acordo com as
    permissões disponibilizadas para esse perfil.

O PaperBot funciona como um assistente geral do PaperTech. Ele fornece
orientações sobre o uso do sistema, mas não realiza operações em nome do
usuário nem consulta diretamente o banco de dados.

## Funcionalidades

### Autenticação e acesso

-   Tela de login e encerramento de sessão.
-   Controle de acesso às páginas protegidas.
-   Diferenciação de permissões entre administrador e funcionário.
-   Proteção de formulários com tokens CSRF.

### Gerenciamento de produtos

O módulo de produtos reúne as páginas para:

-   Listar produtos.
-   Cadastrar produtos.
-   Visualizar informações de um produto.
-   Editar produtos.
-   Excluir produtos.

Os dados dos produtos incluem nome, descrição, categoria, marca, preço,
quantidade em estoque e data de cadastro.

### Gerenciamento de usuários

O módulo de usuários reúne as páginas para:

-   Listar usuários.
-   Cadastrar usuários.
-   Visualizar informações de um usuário.
-   Editar usuários.
-   Excluir usuários.

O cadastro de usuários diferencia os perfis `admin` e `funcionario`. O
gerenciamento de usuários é uma funcionalidade administrativa.

### PaperBot

O PaperBot é o assistente geral do sistema. Ele pode orientar sobre
funcionalidades, navegação e dúvidas comuns relacionadas ao PaperTech.

O assistente:

-   Responde em português, com orientações simples e objetivas.
-   Explica como utilizar os módulos disponíveis.
-   Orienta o usuário sobre procedimentos que devem ser realizados nas
    próprias telas do sistema.

O PaperBot não cadastra, edita ou exclui registros, não consulta o banco
de dados e não confirma informações de estoque em tempo real.

## Tecnologias

As tecnologias identificadas na estrutura e nos arquivos de configuração
do projeto são:

-   **PHP:** aplicação e lógica do servidor.
-   **MySQL/MariaDB:** banco de dados relacional.
-   **PDO:** conexão e comunicação com o banco.
-   **HTML, CSS e JavaScript:** interface e interações no navegador.
-   **Bootstrap:** utilizado na interface, conforme os componentes
    presentes nas páginas.
-   **Python:** execução dos testes automatizados.
-   **Selenium:** automação dos testes de interface.
-   **Gemini API:** geração das respostas do PaperBot.

## Estrutura do projeto

``` text
PaperTech/
│
├── index.php
├── README.md
│
├── config/
│   └── database.php
│
├── database/
│   └── papertech.sql
│
├── includes/
│   ├── auth.php
│   ├── csrf.php
│   ├── footer.php
│   ├── header.php
│   └── sidebar.php
│
├── modules/
│   ├── paperbot/
│   │   ├── api.php
│   │   └── index.php
│   │
│   ├── produtos/
│   │   ├── cadastrar.php
│   │   ├── editar.php
│   │   ├── excluir.php
│   │   ├── index.php
│   │   └── visualizar.php
│   │
│   └── usuarios/
│       ├── cadastrar.php
│       ├── editar.php
│       ├── excluir.php
│       ├── index.php
│       └── visualizar.php
│
├── public/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   │
│   └── assets/
│       ├── css/
│           └── style.css
│      
└── tests/
    ├── apoio_usuarios.py
    ├── config_local.py
    ├── executar_todos.py
    ├── teste_acesso_usuarios.py
    ├── teste_csrf.py
    ├── teste_editar_usuario.py
    ├── teste_excluir_usuario.py
    ├── teste_fase_final.py
    ├── teste_login.py
    ├── teste_usuarios.py
    └── teste_visualizar_usuario.py
```

A pasta `tests/__pycache__/`, quando criada pelo Python, contém arquivos
temporários de cache e não é necessária para a execução manual do
sistema.

## Requisitos

Para executar o projeto localmente, são necessários:

-   Windows ou outro sistema compatível com o ambiente configurado.
-   XAMPP com Apache e MySQL/MariaDB.
-   PHP com suporte a PDO MySQL e cURL.
-   Navegador web atualizado.
-   Python instalado para executar os testes automatizados.
-   Biblioteca Selenium instalada no ambiente Python.
-   Chave de API válida do Google Gemini para utilizar o PaperBot.

As versões registradas no arquivo SQL fornecido foram PHP 8.5.13 e
MariaDB 10.11.19. Outras versões compatíveis podem funcionar, mas não
foram especificadas como requisito mínimo do projeto.

## Instalação e configuração

### 1. Copiar o projeto

Coloque a pasta `PaperTech` no diretório de projetos do XAMPP:

``` text
C:\xampp\htdocs\PaperTech
```

### 2. Iniciar os serviços

Abra o painel de controle do XAMPP e inicie:

-   Apache
-   MySQL

### 3. Criar o banco de dados

Acesse o phpMyAdmin:

``` text
http://localhost/phpmyadmin
```

Crie um banco de dados chamado:

``` text
papertech
```

Em seguida, selecione o banco e importe o arquivo:

``` text
database/papertech.sql
```

O script cria as tabelas `produtos` e `usuarios`, seus índices e
registros de exemplo.

**Atenção:** o SQL contém registros de exemplo e hashes de senha. Não
presuma que as senhas desses registros sejam conhecidas. Para acessar o
sistema, utilize uma conta válida já configurada no banco ou crie/ajuste
uma conta administrativa por um procedimento seguro.

### 4. Conferir a conexão com o banco

O arquivo responsável pela conexão é:

``` text
config/database.php
```

A configuração local fornecida para o projeto é:

``` php
$host = 'localhost';
$banco = 'papertech';
$usuario = 'root';
$senha = '';
```

Se o MySQL/MariaDB do ambiente utilizar outro usuário ou senha, ajuste
esses valores. Não publique credenciais reais em repositórios ou
documentação.

A conexão utiliza PDO, codificação `utf8mb4`, modo de erro por exceção e
prepared statements nativos.

### 5. Acessar o sistema

Com Apache e MySQL em execução, abra:

``` text
http://localhost/PaperTech/
```

A tela de login também está localizada em:

``` text
http://localhost/PaperTech/public/login.php
```

Após autenticar, o usuário poderá acessar as páginas permitidas ao seu
perfil.

## Configuração do PaperBot

O PaperBot utiliza a API do Google Gemini. A chave é mantida fora da
pasta pública do projeto, no caminho:

``` text
C:/xampp/private/gemini_api.php
```

Esse arquivo deve retornar a chave como uma string PHP. Exemplo
ilustrativo:

``` php
<?php
return 'SUA_CHAVE_API';
```

Substitua o texto ilustrativo por uma chave válida, sem compartilhá-la
ou incluí-la no repositório.

A API do PaperBot está em:

``` text
modules/paperbot/api.php
```

A interface do assistente está em:

``` text
modules/paperbot/index.php
```

A integração utiliza cURL para enviar a solicitação ao Gemini. O acesso
à API do PaperBot exige autenticação e validação CSRF.

O arquivo `testar_gemini.php`, na raiz do projeto, é um arquivo de teste
da integração. Ele não é necessário para a navegação normal do sistema e
não deve ser disponibilizado publicamente em um ambiente de produção.

## Execução dos testes

Os testes automatizados ficam na pasta `tests/` e utilizam Python e
Selenium para verificar fluxos do sistema.

### 1. Configurar os dados locais dos testes

O arquivo:

``` text
tests/config_local.py
```

contém os dados de acesso utilizados pelos testes. Configure nele as
credenciais de teste correspondentes a contas existentes no banco de
dados.

Não compartilhe esse arquivo com senhas reais e não publique suas
credenciais em repositórios.

### 2. Preparar o ambiente Python

Instale o Selenium no ambiente Python utilizado para executar os testes:

``` bash
python -m pip install selenium
```

O Selenium também depende de um navegador compatível e de uma
configuração de driver compatível com o ambiente de execução.

### 3. Executar a suíte configurada

Na raiz do projeto (`C:\xampp\htdocs\PaperTech`), execute:

``` bash
python -m tests.executar_todos
```

O arquivo `tests/executar_todos.py` executa os seguintes seis módulos:

1.  `tests.teste_login`
2.  `tests.teste_editar_usuario`
3.  `tests.teste_acesso_usuarios`
4.  `tests.teste_csrf`
5.  `tests.teste_fase_final`
6.  `tests.teste_excluir_usuario`

O executor apresenta o resultado individual de cada módulo e um resumo
final com o total de testes aprovados e reprovados. Cada execução possui
limite de 600 segundos. O processo retorna código de saída `0` quando
todos os módulos configurados são aprovados e `1` quando pelo menos um
falha.

A pasta também contém outros módulos de teste, como `teste_usuarios.py`
e `teste_visualizar_usuario.py`. Eles não fazem parte da lista executada
pelo arquivo `executar_todos.py` na configuração atual e podem ser
executados individualmente, se necessário.

## Segurança e boas práticas

-   Mantenha senhas e chaves de API fora dos arquivos públicos do
    projeto.
-   Não publique `tests/config_local.py` com credenciais reais.
-   Não compartilhe o conteúdo de `C:/xampp/private/gemini_api.php`.
-   Utilize contas de teste próprias para os testes automatizados.
-   Preserve a validação de autenticação e autorização nas páginas
    protegidas.
-   Mantenha a validação CSRF nos formulários e requisições que alteram
    dados.
-   Utilize consultas preparadas para operações no banco de dados.
-   Não exponha mensagens internas, credenciais ou detalhes sensíveis em
    erros apresentados ao usuário.
-   Em ambientes de produção, configure credenciais próprias e
    adequadas; não utilize automaticamente as configurações padrão de
    desenvolvimento do XAMPP.

## Observações

Este README descreve a estrutura e as configurações fornecidas para o
projeto PaperTech. Ajustes futuros na estrutura, nos módulos, nas
dependências ou nos procedimentos de execução devem ser refletidos nesta
documentação.
