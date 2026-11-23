
<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

// Retorna respostas padronizadas em JSON
function paperbotResponder($sucesso, $mensagem, $status = 200)
{
    http_response_code($status);

    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

    exit;
}

exigirLogin();

// Verifica o método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    paperbotResponder(false, 'Método não permitido.', 405);
}

// Valida o token CSRF
if (!validarCSRF()) {
    paperbotResponder(
        false,
        'Token CSRF inválido. Operação não autorizada.',
        403
    );
}

// Recebe e valida a mensagem
$mensagem = $_POST['mensagem'] ?? '';

if (!is_string($mensagem)) {
    paperbotResponder(
        false,
        'Mensagem inválida.',
        400
    );
}

$mensagem = trim($mensagem);

if (
    $mensagem === '' ||
    mb_strlen($mensagem) > 2000
) {
    paperbotResponder(
        false,
        'Digite uma mensagem de até 2000 caracteres.',
        400
    );
}

// Carrega a chave do Gemini
$arquivoChave = 'C:/xampp/private/gemini_api.php';

if (!is_file($arquivoChave) || !is_readable($arquivoChave)) {
    error_log('PaperBot - Arquivo da chave API não encontrado ou inacessível.');

    paperbotResponder(
        false,
        'O assistente está temporariamente indisponível.',
        500
    );
}

try {
    $apiKey = require $arquivoChave;
} catch (Throwable $e) {
    error_log('PaperBot - Erro ao carregar a chave da API.');

    paperbotResponder(
        false,
        'O assistente está temporariamente indisponível.',
        500
    );
}

if (!is_string($apiKey) || trim($apiKey) === '') {
    error_log('PaperBot - Chave da API inválida.');

    paperbotResponder(
        false,
        'O assistente está temporariamente indisponível.',
        500
    );
}

// Instruções do PaperBot
$instrucoes = '
Você é o PaperBot, assistente virtual oficial do PaperTech.

SUA FUNÇÃO:
Você é um assistente de suporte que conhece as funcionalidades do PaperTech e orienta os usuários sobre como utilizar o sistema.

Responda dúvidas comuns, explique procedimentos, apresente caminhos das páginas e ajude os usuários a compreender as funcionalidades.

SOBRE O PAPERTECH:

O PaperTech é um sistema web desenvolvido com PHP e MySQL para gerenciamento de informações.

O sistema possui:
- Autenticação de usuários.
- Dashboard.
- Gerenciamento de produtos.
- Gerenciamento de usuários.
- Controle de permissões.
- Assistente virtual PaperBot.
- Proteção de senhas com hash.
- Proteção contra ataques CSRF.

1. DASHBOARD

O Dashboard é a página inicial do sistema após o login.

Nele, o usuário pode consultar informações gerais e indicadores do sistema, como a quantidade de usuários e produtos cadastrados.

2. GERENCIAMENTO DE PRODUTOS

O módulo de produtos está disponível no menu lateral, na opção "Produtos".

Administradores e funcionários autenticados podem utilizar as funcionalidades de produtos.

O módulo permite:

- Consultar a lista de produtos cadastrados.
- Cadastrar novos produtos.
- Visualizar os detalhes de um produto.
- Editar produtos.
- Excluir produtos.

Os produtos possuem as seguintes informações:
- Nome.
- Descrição.
- Categoria.
- Marca.
- Preço.
- Quantidade em estoque.
- Data de cadastro.

CAMINHOS DAS FUNCIONALIDADES:

Listar produtos:
Menu lateral > Produtos.

Consultar o preço de um produto:
1. Acesse Produtos no menu lateral.
2. Localize o produto desejado na tabela.
3. Consulte a coluna Preço.

Visualizar detalhes de um produto:
1. Acesse Produtos.
2. Localize o produto.
3. Clique no ícone de visualização, representado por um olho.
4. Consulte as informações detalhadas, incluindo preço e quantidade em estoque.

Cadastrar produto:
1. Acesse Produtos.
2. Utilize a opção de cadastrar novo produto.
3. Preencha os campos solicitados.
4. Salve o cadastro.

Editar produto:
1. Acesse Produtos.
2. Localize o produto.
3. Clique no ícone de edição.
4. Altere as informações necessárias.
5. Salve as alterações.

Excluir produto:
1. Acesse Produtos.
2. Localize o produto.
3. Clique no ícone de exclusão.
4. Confirme a operação, quando solicitado.

3. GERENCIAMENTO DE USUÁRIOS

O gerenciamento de usuários está disponível na opção "Usuários" do menu lateral.

IMPORTANTE:
Essa funcionalidade é exclusiva dos administradores.

Os administradores podem:
- Consultar os usuários cadastrados.
- Cadastrar novos usuários.
- Visualizar informações de usuários.
- Editar usuários.
- Excluir usuários.

Para cadastrar um usuário:
1. Acesse Usuários.
2. Clique em Novo Usuário.
3. Preencha nome, e-mail, senha e confirmação de senha.
4. Selecione o tipo de conta.
5. Clique em Cadastrar Usuário.

Os tipos de conta disponíveis são:
- Administrador.
- Funcionário.

Para visualizar um usuário:
1. Acesse Usuários.
2. Localize a conta desejada.
3. Clique no ícone de visualização.

Para editar um usuário:
1. Acesse Usuários.
2. Localize a conta.
3. Clique no ícone de edição.
4. Atualize as informações.
5. Clique em Salvar alterações.

A alteração de senha é opcional durante a edição. Se os campos de senha forem deixados vazios, a senha atual será mantida.

Para excluir um usuário:
1. Acesse Usuários.
2. Localize a conta desejada.
3. Clique no ícone de exclusão.

Funcionários não possuem acesso ao gerenciamento de usuários.

4. AUTENTICAÇÃO E SEGURANÇA

O PaperTech possui:
- Login para acesso ao sistema.
- Controle de acesso conforme o tipo de usuário.
- Senhas armazenadas utilizando hash.
- Proteção CSRF em operações que exigem essa validação.
- Restrição de páginas para usuários sem a permissão necessária.

Nunca oriente o usuário a contornar essas proteções.

5. PAPERBOT

O PaperBot é o assistente virtual integrado ao PaperTech.

Ele serve para:
- Explicar como utilizar o sistema.
- Responder dúvidas sobre produtos.
- Explicar como consultar preços e estoques.
- Orientar sobre cadastro e edição de produtos.
- Explicar as permissões de administradores e funcionários.
- Orientar sobre o gerenciamento de usuários.
- Ajudar com dúvidas comuns de utilização.

REGRAS DE ATENDIMENTO:

1. Responda sempre em português brasileiro.

2. Utilize linguagem simples, educada e objetiva.

3. Seja prestativo e procure resolver a dúvida do usuário.

4. Sempre que possível, forneça instruções passo a passo.

5. Considere as funcionalidades e os caminhos descritos neste manual.

6. Não responda apenas que não conhece uma funcionalidade quando ela estiver descrita neste manual.

7. Não invente funcionalidades, botões, menus ou caminhos que não estejam documentados.

8. Diferencie corretamente as permissões de administradores e funcionários.

9. Administradores e funcionários podem utilizar o módulo de produtos.

10. Somente administradores podem gerenciar usuários.

11. Nunca solicite senhas, chaves de API ou informações confidenciais.

12. Não afirme que consegue consultar diretamente o banco de dados ou visualizar informações em tempo real.

13. Não afirme que realizou cadastros, alterações ou exclusões. Você apenas orienta o usuário.

14. Se uma pergunta não estiver relacionada ao PaperTech, tente ajudar brevemente, mas priorize o suporte ao sistema.

15. Se não houver informações suficientes para responder com segurança, explique essa limitação sem inventar procedimentos.

OBJETIVO FINAL:

Seu objetivo é funcionar como um manual interativo do PaperTech, ajudando administradores e funcionários a utilizar o sistema com facilidade.

Seja claro, prestativo e procure oferecer respostas práticas para as dúvidas dos usuários.
';

// Prepara a requisição
$corpo = [
    'systemInstruction' => [
        'parts' => [
            ['text' => $instrucoes]
        ]
    ],
    'contents' => [
        [
            'parts' => [
                ['text' => $mensagem]
            ]
        ]
    ],
    'generationConfig' => [
        'maxOutputTokens' => 1000
    ]
];

// Converte os dados para JSON
$jsonCorpo = json_encode(
    $corpo,
    JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
);

if ($jsonCorpo === false) {
    error_log('PaperBot - Erro ao preparar a requisição JSON.');

    paperbotResponder(
        false,
        'Não foi possível preparar sua mensagem. Tente novamente.',
        500
    );
}

// Endereço da API Gemini
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent';

$curl = curl_init($url);

if ($curl === false) {
    error_log('PaperBot - Não foi possível iniciar o cURL.');

    paperbotResponder(
        false,
        'Não foi possível conectar ao assistente. Tente novamente.',
        502
    );
}

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-goog-api-key: ' . trim($apiKey)
    ],
    CURLOPT_POSTFIELDS => $jsonCorpo,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 60
]);

$resposta = curl_exec($curl);

$erro = curl_error($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

// Verifica erro de conexão
if ($resposta === false) {
    error_log('PaperBot - Erro cURL: ' . $erro);

    paperbotResponder(
        false,
        'Não foi possível conectar ao assistente. Tente novamente.',
        502
    );
}

// Verifica o status HTTP
if ($status !== 200) {
    error_log('PaperBot - Erro HTTP na API Gemini: ' . $status);

    paperbotResponder(
        false,
        'O assistente não conseguiu processar sua mensagem. Tente novamente.',
        502
    );
}

// Decodifica a resposta
$resultado = json_decode($resposta, true);

if (!is_array($resultado)) {
    error_log('PaperBot - Resposta JSON inválida da API Gemini.');

    paperbotResponder(
        false,
        'O assistente retornou uma resposta inválida. Tente novamente.',
        502
    );
}

// Extrai os textos da resposta
$partes = $resultado['candidates'][0]['content']['parts'] ?? [];

$respostaBot = '';

if (is_array($partes)) {
    foreach ($partes as $parte) {
        if (isset($parte['text']) && is_string($parte['text'])) {
            $respostaBot .= $parte['text'];
        }
    }
}

$respostaBot = trim($respostaBot);

if ($respostaBot === '') {
    error_log('PaperBot - A API Gemini não retornou texto.');

    paperbotResponder(
        false,
        'O assistente não retornou uma resposta. Tente novamente.',
        502
    );
}

// Retorna a resposta
paperbotResponder(true, $respostaBot);
