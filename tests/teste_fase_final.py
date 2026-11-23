import sys

from urllib.parse import urlparse, parse_qs

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from tests.apoio_usuarios import (
    iniciar_navegador,
    login_administrador,
    login_funcionario,
    localizar_linha_por_email,
    URL_PAPERTECH
)

from tests.config_local import EMAIL_FUNCIONARIO


URL_LISTA = URL_PAPERTECH + "modules/usuarios/index.php"
URL_EDICAO = URL_PAPERTECH + "modules/usuarios/editar.php"
URL_EXCLUSAO = URL_PAPERTECH + "modules/usuarios/excluir.php"
URL_DASHBOARD = URL_PAPERTECH + "public/dashboard.php"
URL_LOGOUT = URL_PAPERTECH + "public/logout.php"

MENSAGEM_ACESSO_NEGADO = (
    "Acesso negado. Você não possui permissão."
)

MENSAGEM_ID_INVALIDO = "ID de usuário inválido."


# ============================================================
# FUNÇÕES AUXILIARES
# ============================================================

def obter_id_funcionario(navegador):
    """
    Localiza o funcionário de teste na listagem
    e obtém seu ID pelo link de edição.
    """

    navegador.get(URL_LISTA)

    linha = localizar_linha_por_email(
        navegador,
        EMAIL_FUNCIONARIO
    )

    assert linha is not None, (
        "Funcionário de teste não encontrado."
    )

    link = linha.find_element(
        By.CSS_SELECTOR,
        "a[href*='editar.php?id=']"
    )

    endereco = link.get_attribute("href")

    parametros = parse_qs(urlparse(endereco).query)

    identificador = parametros.get("id", [None])[0]

    assert identificador is not None, (
        "Não foi possível obter o ID do funcionário."
    )

    return identificador


def iniciar_como_administrador():
    navegador = iniciar_navegador()

    login_administrador(navegador)

    identificador = obter_id_funcionario(navegador)

    return navegador, identificador


def verificar_funcionario_preservado(navegador):
    """
    Confirma que o funcionário continua cadastrado
    e que seus dados principais permanecem disponíveis.
    """

    navegador.get(URL_LISTA)

    linha = localizar_linha_por_email(
        navegador,
        EMAIL_FUNCIONARIO
    )

    assert linha is not None, (
        "O funcionário de teste foi removido indevidamente."
    )

    return linha


def enviar_post_edicao(navegador, identificador, dados):
    """
    Envia POST diretamente pelo navegador.
    Isso permite testar validações do PHP sem depender
    da validação HTML do formulário.
    """

    navegador.get(
        f"{URL_EDICAO}?id={identificador}"
    )

    espera = WebDriverWait(navegador, 10)

    campo_token = espera.until(
        EC.presence_of_element_located(
            (By.NAME, "csrf_token")
        )
    )

    token = campo_token.get_attribute("value")

    dados["csrf_token"] = token

    resultado = navegador.execute_async_script("""
        const dados = arguments[0];
        const finalizar = arguments[1];

        fetch(window.location.href, {
            method: "POST",
            headers: {
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams(dados)
        })
        .then(async resposta => {
            const corpo = await resposta.text();

            finalizar({
                status: resposta.status,
                corpo: corpo
            });
        })
        .catch(erro => {
            finalizar({
                erro: erro.toString()
            });
        });
    """, dados)

    assert "erro" not in resultado, (
        f"Falha na requisição: {resultado.get('erro')}"
    )

    return resultado


def verificar_alerta_de_erro(resultado):
    assert resultado["status"] == 200, (
        f"Status inesperado: {resultado['status']}"
    )

    assert "alert-danger" in resultado["corpo"], (
        "O servidor não apresentou o alerta de validação esperado."
    )


# ============================================================
# TESTE 1 - FUNCIONÁRIO NÃO PODE ACESSAR A EDIÇÃO
# ============================================================

def testar_acesso_direto_edicao_funcionario():

    navegador, identificador = iniciar_como_administrador()

    try:
        navegador.get(URL_LOGOUT)

        login_funcionario(navegador)

        navegador.get(
            f"{URL_EDICAO}?id={identificador}"
        )

        assert MENSAGEM_ACESSO_NEGADO in navegador.page_source

        print(
            "APROVADO: funcionário não acessa edição de usuários."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 2 - FUNCIONÁRIO NÃO PODE ACESSAR A EXCLUSÃO
# ============================================================

def testar_acesso_direto_exclusao_funcionario():

    navegador, identificador = iniciar_como_administrador()

    try:
        navegador.get(URL_LOGOUT)

        login_funcionario(navegador)

        navegador.get(
            f"{URL_EXCLUSAO}?id={identificador}"
        )

        assert MENSAGEM_ACESSO_NEGADO in navegador.page_source

        print(
            "APROVADO: funcionário não acessa exclusão de usuários."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 3 - ID AUSENTE NA EDIÇÃO
# ============================================================

def testar_edicao_sem_id():

    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(URL_EDICAO)

        assert MENSAGEM_ID_INVALIDO in navegador.page_source

        print(
            "APROVADO: edição sem ID foi bloqueada."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 4 - ID NÃO NUMÉRICO NA EDIÇÃO
# ============================================================

def testar_edicao_id_nao_numerico():

    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            f"{URL_EDICAO}?id=abc"
        )

        assert MENSAGEM_ID_INVALIDO in navegador.page_source

        print(
            "APROVADO: ID não numérico foi bloqueado."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 5 - ID EXCESSIVAMENTE GRANDE
# ============================================================

def testar_edicao_id_excessivamente_grande():

    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        id_grande = "999999999999999999999999999999999999"

        navegador.get(
            f"{URL_EDICAO}?id={id_grande}"
        )

        assert MENSAGEM_ID_INVALIDO in navegador.page_source

        print(
            "APROVADO: ID excessivamente grande foi bloqueado."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 6 - LOGOUT ENCERRA A SESSÃO
# ============================================================

def testar_logout():

    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        assert "dashboard.php" in navegador.current_url

        navegador.get(URL_LOGOUT)

        WebDriverWait(navegador, 10).until(
            EC.url_contains("login.php")
        )

        assert "login.php" in navegador.current_url

        print(
            "APROVADO: logout encerrou a sessão."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 7 - DASHBOARD BLOQUEADO APÓS LOGOUT
# ============================================================

def testar_dashboard_apos_logout():

    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(URL_LOGOUT)

        navegador.get(URL_DASHBOARD)

        WebDriverWait(navegador, 10).until(
            EC.url_contains("login.php")
        )

        assert "login.php" in navegador.current_url

        print(
            "APROVADO: dashboard bloqueado após logout."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 8 - SENHAS DIFERENTES NÃO ALTERAM O CADASTRO
# ============================================================

def testar_integridade_senhas_diferentes():

    navegador, identificador = iniciar_como_administrador()

    try:
        linha_original = verificar_funcionario_preservado(
            navegador
        )

        nome_original = linha_original.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(2)"
        ).text.strip()

        email_original = linha_original.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(3)"
        ).text.strip()

        tipo_original = linha_original.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(4)"
        ).text.strip()

        dados = {
            "nome": nome_original,
            "email": email_original,
            "tipo": "funcionario",
            "senha": "SenhaTeste123!",
            "confirmar_senha": "SenhaDiferente123!"
        }

        resultado = enviar_post_edicao(
            navegador,
            identificador,
            dados
        )

        assert "As senhas não coincidem." in resultado["corpo"]

        linha_atual = verificar_funcionario_preservado(
            navegador
        )

        assert linha_atual.find_element(
            By.CSS_SELECTOR, "td:nth-child(2)"
        ).text.strip() == nome_original

        assert linha_atual.find_element(
            By.CSS_SELECTOR, "td:nth-child(3)"
        ).text.strip() == email_original

        assert linha_atual.find_element(
            By.CSS_SELECTOR, "td:nth-child(4)"
        ).text.strip() == tipo_original

        print(
            "APROVADO: senhas diferentes não alteraram o cadastro."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 9 - EXCLUSÃO SEM CSRF NÃO ALTERA O BANCO
# ============================================================

def testar_integridade_exclusao_sem_csrf():

    navegador, identificador = iniciar_como_administrador()

    try:
        navegador.get(URL_LISTA)

        resultado = navegador.execute_async_script("""
            const id = arguments[0];
            const finalizar = arguments[1];

            fetch(
                "excluir.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        id: id
                    })
                }
            )
            .then(async resposta => {
                finalizar({
                    status: resposta.status,
                    corpo: await resposta.text()
                });
            })
            .catch(erro => {
                finalizar({
                    erro: erro.toString()
                });
            });
        """, identificador)

        assert "erro" not in resultado, (
            f"Falha na requisição: {resultado.get('erro')}"
        )

        assert resultado["status"] == 403, (
            "A exclusão sem CSRF deveria retornar HTTP 403."
        )

        verificar_funcionario_preservado(navegador)

        print(
            "APROVADO: exclusão sem CSRF bloqueada e cadastro preservado."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 10 - NOME ACIMA DE 150 CARACTERES
# ============================================================

def testar_validacao_nome_acima_limite_servidor():

    navegador, identificador = iniciar_como_administrador()

    try:
        linha = verificar_funcionario_preservado(navegador)

        nome_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(2)"
        ).text.strip()

        email_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(3)"
        ).text.strip()

        dados = {
            "nome": "N" * 151,
            "email": email_original,
            "tipo": "funcionario",
            "senha": "",
            "confirmar_senha": ""
        }

        resultado = enviar_post_edicao(
            navegador,
            identificador,
            dados
        )

        verificar_alerta_de_erro(resultado)

        linha_atual = verificar_funcionario_preservado(
            navegador
        )

        nome_atual = linha_atual.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(2)"
        ).text.strip()

        assert nome_atual == nome_original

        print(
            "APROVADO: servidor rejeitou nome acima de 150 caracteres."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 11 - E-MAIL INVÁLIDO
# ============================================================

def testar_validacao_email_invalido_servidor():

    navegador, identificador = iniciar_como_administrador()

    try:
        linha = verificar_funcionario_preservado(navegador)

        nome_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(2)"
        ).text.strip()

        email_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(3)"
        ).text.strip()

        dados = {
            "nome": nome_original,
            "email": "email-invalido",
            "tipo": "funcionario",
            "senha": "",
            "confirmar_senha": ""
        }

        resultado = enviar_post_edicao(
            navegador,
            identificador,
            dados
        )

        verificar_alerta_de_erro(resultado)

        linha_atual = verificar_funcionario_preservado(
            navegador
        )

        email_atual = linha_atual.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(3)"
        ).text.strip()

        assert email_atual == email_original

        print(
            "APROVADO: servidor rejeitou e-mail inválido."
        )

    finally:
        navegador.quit()


# ============================================================
# TESTE 12 - TIPO DE USUÁRIO INVÁLIDO
# ============================================================

def testar_validacao_tipo_invalido_servidor():

    navegador, identificador = iniciar_como_administrador()

    try:
        linha = verificar_funcionario_preservado(navegador)

        nome_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(2)"
        ).text.strip()

        email_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(3)"
        ).text.strip()

        tipo_original = linha.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(4)"
        ).text.strip()

        dados = {
            "nome": nome_original,
            "email": email_original,
            "tipo": "superusuario",
            "senha": "",
            "confirmar_senha": ""
        }

        resultado = enviar_post_edicao(
            navegador,
            identificador,
            dados
        )

        verificar_alerta_de_erro(resultado)

        linha_atual = verificar_funcionario_preservado(
            navegador
        )

        tipo_atual = linha_atual.find_element(
            By.CSS_SELECTOR,
            "td:nth-child(4)"
        ).text.strip()

        assert tipo_atual == tipo_original

        print(
            "APROVADO: servidor rejeitou tipo de usuário inválido."
        )

    finally:
        navegador.quit()


# ============================================================
# EXECUÇÃO DOS 12 TESTES
# ============================================================

if __name__ == "__main__":

    testes = [
        testar_acesso_direto_edicao_funcionario,
        testar_acesso_direto_exclusao_funcionario,
        testar_edicao_sem_id,
        testar_edicao_id_nao_numerico,
        testar_edicao_id_excessivamente_grande,
        testar_logout,
        testar_dashboard_apos_logout,
        testar_integridade_senhas_diferentes,
        testar_integridade_exclusao_sem_csrf,
        testar_validacao_nome_acima_limite_servidor,
        testar_validacao_email_invalido_servidor,
        testar_validacao_tipo_invalido_servidor
    ]

    aprovados = 0
    reprovados = 0

    print("\n======================================")
    print(" PAPERTECH - TESTES COMPLEMENTARES")
    print("======================================\n")

    for teste in testes:

        try:
            print(f"\nExecutando: {teste.__name__}")

            teste()

            aprovados += 1

        except Exception as erro:

            reprovados += 1

            print(
                f"REPROVADO: {teste.__name__}"
            )

            print(f"Motivo: {erro}")

    print("\n======================================")
    print("          RESULTADO FINAL")
    print("======================================")

    print(f"Total: {len(testes)}")
    print(f"Aprovados: {aprovados}")
    print(f"Reprovados: {reprovados}")

    if reprovados == 0:
        print("\nTODOS OS 12 TESTES FORAM APROVADOS!")
    else:
        print("\nEXISTEM TESTES QUE PRECISAM DE CORREÇÃO.")
        
sys.exit(1 if reprovados > 0 else 0)