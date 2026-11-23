
<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

exigirLogin();

$paginaTitulo = 'PaperBot';

?>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<style>
    .paperbot-page-header {
        margin-bottom: 24px;
    }

    .paperbot-page-header h2 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .paperbot-page-header p {
        color: #667085;
        margin-bottom: 0;
    }

    .paperbot-container {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(16, 24, 40, 0.06);
        border: 1px solid #edf0f5;
        display: flex;
        flex-direction: column;
        height: min(72vh, 720px);
        min-height: 480px;
        overflow: hidden;
    }

    .paperbot-header {
        background: #563d7c;
        color: #ffffff;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .paperbot-avatar {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .paperbot-avatar svg {
        width: 25px;
        height: 25px;
    }

    .paperbot-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .paperbot-header p {
        font-size: 13px;
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
    }

    .paperbot-status {
        margin-left: auto;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .paperbot-status-dot {
        width: 8px;
        height: 8px;
        background: #75e0a7;
        border-radius: 50%;
    }

    .paperbot-messages {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        background: #fafbfe;
    }

    .paperbot-message {
        max-width: 75%;
        width: fit-content;
        padding: 13px 16px;
        margin-bottom: 16px;
        border-radius: 12px;
        line-height: 1.6;
        font-size: 14px;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .paperbot-message.bot {
        background: #eeeaf5;
        color: #344054;
        margin-right: auto;
        border-bottom-left-radius: 4px;
    }

    .paperbot-message.user {
        background: #563d7c;
        color: #ffffff;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .paperbot-message p {
        margin: 0 0 12px;
    }

    .paperbot-message p:last-child {
        margin-bottom: 0;
    }

    .paperbot-message ol,
    .paperbot-message ul {
        padding-left: 22px;
        margin: 10px 0;
    }

    .paperbot-message li {
        margin-bottom: 5px;
    }

    .paperbot-message strong {
        font-weight: 700;
    }

    .paperbot-input-area {
        padding: 18px 22px;
        background: #ffffff;
        border-top: 1px solid #edf0f5;
    }

    .paperbot-form {
        display: flex;
        align-items: flex-end;
        gap: 12px;
    }

    .paperbot-form textarea {
        flex: 1;
        resize: none;
        min-height: 48px;
        max-height: 120px;
        padding: 13px 15px;
        border: 1px solid #d0d5dd;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .paperbot-form textarea:focus {
        border-color: #563d7c;
        box-shadow: 0 0 0 3px rgba(86, 61, 124, 0.10);
    }

    .paperbot-send {
        min-height: 48px;
        padding: 0 22px;
        border: none;
        border-radius: 10px;
        background: #563d7c;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .paperbot-send:hover {
        background: #452e68;
    }

    .paperbot-send:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .paperbot-hint {
        margin: 9px 0 0;
        color: #98a2b3;
        font-size: 11px;
    }

    @media (max-width: 600px) {
        .paperbot-container {
            height: 70vh;
            min-height: 400px;
        }

        .paperbot-messages {
            padding: 16px;
        }

        .paperbot-message {
            max-width: 90%;
        }

        .paperbot-input-area {
            padding: 12px;
        }

        .paperbot-form {
            gap: 8px;
        }

        .paperbot-send {
            padding: 0 14px;
        }

        .paperbot-status {
            display: none;
        }
    }
</style>

<div class="main-container">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="content">

        <div class="paperbot-page-header">
            <h2>PaperBot</h2>

            <p>
                Converse com o assistente virtual e tire suas dúvidas sobre o PaperTech.
            </p>
        </div>

        <div class="paperbot-container">

            <div class="paperbot-header">

                <div class="paperbot-avatar">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8
                                 8.5 8.5 0 0 1-7.6 4.7
                                 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7
                                 a8.4 8.4 0 0 1-.9-3.8
                                 8.5 8.5 0 0 1 4.7-7.6
                                 8.4 8.4 0 0 1 3.8-.9h.5
                                 a8.5 8.5 0 0 1 8 8v.5z"/>
                    </svg>
                </div>

                <div>
                    <h3>PaperBot</h3>
                    <p>Assistente virtual do PaperTech</p>
                </div>

                <div class="paperbot-status">
                    <span class="paperbot-status-dot"></span>
                    Online
                </div>

            </div>

            <div class="paperbot-messages" id="mensagens">

                <div class="paperbot-message bot">
                    Olá! Sou o PaperBot. Como posso ajudar você a utilizar o PaperTech?
                </div>

            </div>

            <div class="paperbot-input-area">

                <form class="paperbot-form" id="formulario">

                    <?= campoCSRF() ?>

                    <textarea
                        id="mensagem"
                        placeholder="Digite sua mensagem..."
                        maxlength="2000"
                        required
                    ></textarea>

                    <button
                        type="submit"
                        class="paperbot-send"
                        id="botaoEnviar"
                    >
                        Enviar
                    </button>

                </form>

                <p class="paperbot-hint">
                    Pressione Enter para enviar ou Shift + Enter para pular uma linha.
                </p>

            </div>

        </div>

    </main>

</div>

<script>
const formulario = document.getElementById('formulario');
const campoMensagem = document.getElementById('mensagem');
const mensagens = document.getElementById('mensagens');
const botaoEnviar = document.getElementById('botaoEnviar');

function formatarMensagem(texto) {

    // Protege contra inserção de HTML
    const seguro = texto.replace(/[&<>"']/g, caractere => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[caractere]);

    const linhas = seguro.split(/\r?\n/);

    let html = '';
    let listaAberta = '';

    linhas.forEach(linha => {

        const numerada = linha.match(/^\s*\d+\.\s+(.*)$/);
        const marcadores = linha.match(/^\s*[-*]\s+(.*)$/);

        if (numerada || marcadores) {

            const tipoLista = numerada ? 'ol' : 'ul';
            const conteudo = numerada ? numerada[1] : marcadores[1];

            if (listaAberta !== tipoLista) {

                if (listaAberta) {
                    html += `</${listaAberta}>`;
                }

                html += `<${tipoLista}>`;
                listaAberta = tipoLista;
            }

            html += `<li>${conteudo.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')}</li>`;

        } else {

            if (listaAberta) {
                html += `</${listaAberta}>`;
                listaAberta = '';
            }

            if (linha.trim() !== '') {
                html += `<p>${linha.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')}</p>`;
            }

        }

    });

    if (listaAberta) {
        html += `</${listaAberta}>`;
    }

    return html;
}

function adicionarMensagem(texto, tipo) {

    const elemento = document.createElement('div');

    elemento.classList.add('paperbot-message', tipo);

    elemento.innerHTML = formatarMensagem(texto);

    mensagens.appendChild(elemento);

    mensagens.scrollTop = mensagens.scrollHeight;
}

formulario.addEventListener('submit', async function(evento) {

    evento.preventDefault();

    const mensagem = campoMensagem.value.trim();

    if (!mensagem || botaoEnviar.disabled) {
        return;
    }

    // Verifica se o token CSRF está disponível
    const campoCSRF = formulario.querySelector(
        'input[name="csrf_token"]'
    );

    if (!campoCSRF || !campoCSRF.value) {
        adicionarMensagem(
            'Não foi possível validar sua sessão. Atualize a página e tente novamente.',
            'bot'
        );
        return;
    }

    adicionarMensagem(mensagem, 'user');

    campoMensagem.value = '';

    botaoEnviar.disabled = true;
    botaoEnviar.textContent = 'Aguarde...';

    const carregando = document.createElement('div');

    carregando.className = 'paperbot-message bot';
    carregando.textContent = 'PaperBot está pensando...';

    mensagens.appendChild(carregando);

    mensagens.scrollTop = mensagens.scrollHeight;

    try {

        const dados = new URLSearchParams();

        dados.append('mensagem', mensagem);
        dados.append('csrf_token', campoCSRF.value);

        const resposta = await fetch('api.php', {

            method: 'POST',

            credentials: 'same-origin',

            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },

            body: dados.toString()

        });

        // Trata respostas que não sejam JSON válido
        let resultado;

        try {
            resultado = await resposta.json();
        } catch (erro) {
            throw new Error(
                'O servidor retornou uma resposta inválida. Tente novamente.'
            );
        }

        carregando.remove();

        if (!resposta.ok || !resultado.sucesso) {
            throw new Error(
                resultado.mensagem || 'Não foi possível obter uma resposta.'
            );
        }

        adicionarMensagem(resultado.mensagem, 'bot');

    } catch (erro) {

        carregando.remove();

        adicionarMensagem(
            erro.message || 'Erro ao comunicar com o PaperBot.',
            'bot'
        );

    } finally {

        botaoEnviar.disabled = false;
        botaoEnviar.textContent = 'Enviar';

        campoMensagem.focus();

    }

});

campoMensagem.addEventListener('keydown', function(evento) {

    if (evento.key === 'Enter' && !evento.shiftKey) {
        evento.preventDefault();
        formulario.requestSubmit();
    }

});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
