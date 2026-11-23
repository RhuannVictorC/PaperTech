
from selenium.webdriver.common.by import By

from tests.apoio_usuarios import (
    iniciar_navegador,
    login_administrador,
    localizar_linha_por_email,
    EMAIL_ADMIN,
    EMAIL_FUNCIONARIO,
    URL_PAPERTECH
)


def enviar_post_exclusao(navegador, id_usuario, token):
    return navegador.execute_async_script("""
        const id = arguments[0];
        const token = arguments[1];
        const concluir = arguments[2];

        const dados = new URLSearchParams();

        dados.append("id", id);
        dados.append("csrf_token", token);

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
                url: resposta.url,
                texto: await resposta.text()
            });
        })
        .catch(erro => {
            concluir({
                erro: String(erro)
            });
        });
    """, str(id_usuario), token)


def obter_token_csrf(navegador, linha):
    formulario = linha.find_element(By.TAG_NAME, "form")

    return formulario.find_element(
        By.NAME, "csrf_token"
    ).get_attribute("value")


def obter_id_usuario(linha):
    return linha.find_element(
        By.CSS_SELECTOR, "td:nth-child(1)"
    ).text.strip()


def testar_id_invalido_exclusao():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_admin = localizar_linha_por_email(
            navegador, EMAIL_ADMIN
        )

        assert linha_admin is not None

        token = obter_token_csrf(
            navegador, linha_admin
        )

        resultado = enviar_post_exclusao(
            navegador, "0", token
        )

        assert "erro=id_invalido" in resultado["url"], resultado

        print(
            "APROVADO: exclusão com ID inválido foi bloqueada."
        )

    finally:
        navegador.quit()


def testar_usuario_inexistente_exclusao():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_admin = localizar_linha_por_email(
            navegador, EMAIL_ADMIN
        )

        assert linha_admin is not None

        token = obter_token_csrf(
            navegador, linha_admin
        )

        resultado = enviar_post_exclusao(
            navegador, "999999", token
        )

        assert "erro=nao_encontrado" in resultado["url"], resultado

        print(
            "APROVADO: exclusão de usuário inexistente foi bloqueada."
        )

    finally:
        navegador.quit()


def testar_exclusao_propria_conta():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_admin = localizar_linha_por_email(
            navegador, EMAIL_ADMIN
        )

        assert linha_admin is not None

        id_admin = obter_id_usuario(linha_admin)
        token = obter_token_csrf(navegador, linha_admin)

        resultado = enviar_post_exclusao(
            navegador, id_admin, token
        )

        assert "erro=propria_conta" in resultado["url"], resultado

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_admin = localizar_linha_por_email(
            navegador, EMAIL_ADMIN
        )

        assert linha_admin is not None, (
            "A conta administrativa foi removida indevidamente."
        )

        print(
            "APROVADO: o administrador não consegue "
            "excluir a própria conta."
        )

    finally:
        navegador.quit()


def testar_exclusao_real_funcionario():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_funcionario = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha_funcionario is not None, (
            "Funcionário de teste não encontrado. "
            "Verifique se ele ainda existe."
        )

        id_funcionario = obter_id_usuario(
            linha_funcionario
        )

        token = obter_token_csrf(
            navegador, linha_funcionario
        )

        resultado = enviar_post_exclusao(
            navegador,
            id_funcionario,
            token
        )

        assert "excluido=1" in resultado["url"], resultado

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_removida = localizar_linha_por_email(
            navegador, EMAIL_FUNCIONARIO
        )

        assert linha_removida is None, (
            "O funcionário ainda aparece na listagem."
        )

        print(
            "APROVADO: funcionário excluído e "
            "ausência confirmada na listagem."
        )

    finally:
        navegador.quit()


def testar_exclusao_por_get():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH
            + "modules/usuarios/excluir.php?id=999999"
        )

        assert navegador.current_url.endswith("index.php"), (
            "O acesso GET não redirecionou para a listagem."
        )

        print(
            "APROVADO: requisição GET não executa exclusões."
        )

    finally:
        navegador.quit()



if __name__ == "__main__":
    testar_id_invalido_exclusao()
    testar_usuario_inexistente_exclusao()
    testar_exclusao_propria_conta()
    testar_exclusao_real_funcionario()
    testar_exclusao_por_get()
