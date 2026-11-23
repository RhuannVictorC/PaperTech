
from selenium.webdriver.common.by import By

from tests.apoio_usuarios import (
    iniciar_navegador,
    login_administrador,
    localizar_linha_por_email,
    EMAIL_FUNCIONARIO,
    URL_PAPERTECH
)


def enviar_requisicao_csrf(navegador, id_usuario, token):
    return navegador.execute_async_script("""
        const id = arguments[0];
        const token = arguments[1];
        const concluir = arguments[2];

        const dados = new URLSearchParams();
        dados.append("id", id);

        if (token !== null) {
            dados.append("csrf_token", token);
        }

        fetch(
            "/PaperTech/modules/usuarios/excluir.php",
            {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type":
                    "application/x-www-form-urlencoded"
                },
                body: dados.toString()
            }
        )
        .then(async resposta => {
            concluir({
                status: resposta.status,
                texto: await resposta.text(),
                url: resposta.url
            });
        })
        .catch(erro => {
            concluir({
                erro: String(erro)
            });
        });
    """, id_usuario, token)


def testar_csrf_ausente():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha is not None, (
            "Funcionário de teste não encontrado."
        )

        link = linha.find_element(
            By.CSS_SELECTOR, "a[href*='editar.php?id=']"
        ).get_attribute("href")

        id_usuario = link.split("id=")[1].split("&")[0]

        resultado = enviar_requisicao_csrf(
            navegador, id_usuario, None
        )

        assert resultado["status"] == 403, resultado

        navegador.refresh()

        linha = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha is not None, (
            "O usuário foi removido sem token CSRF."
        )

        print(
            "APROVADO: requisição sem token CSRF "
            "foi rejeitada com HTTP 403."
        )

    finally:
        navegador.quit()


def testar_csrf_invalido():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha is not None, (
            "Funcionário de teste não encontrado."
        )

        link = linha.find_element(
            By.CSS_SELECTOR, "a[href*='editar.php?id=']"
        ).get_attribute("href")

        id_usuario = link.split("id=")[1].split("&")[0]

        resultado = enviar_requisicao_csrf(
            navegador,
            id_usuario,
            "token_invalido_123"
        )

        assert resultado["status"] == 403, resultado

        navegador.refresh()

        linha = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha is not None, (
            "O usuário foi removido com token inválido."
        )

        print(
            "APROVADO: requisição com token CSRF "
            "inválido foi rejeitada com HTTP 403."
        )

    finally:
        navegador.quit()


if __name__ == "__main__":
    testar_csrf_ausente()
    testar_csrf_invalido()
