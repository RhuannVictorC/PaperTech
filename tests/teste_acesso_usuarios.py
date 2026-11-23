
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from tests.apoio_usuarios import (
    iniciar_navegador,
    login_administrador,
    login_funcionario,
    URL_PAPERTECH
)


def testar_acesso_sem_login():
    navegador = iniciar_navegador()

    try:
        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        WebDriverWait(navegador, 10).until(
            EC.url_contains("login.php")
        )

        assert "login.php" in navegador.current_url

        print("APROVADO: acesso sem login foi bloqueado.")

    finally:
        navegador.quit()


def testar_acesso_funcionario():
    navegador = iniciar_navegador()

    try:
        login_funcionario(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        WebDriverWait(navegador, 10).until(
            lambda driver:
            "Acesso negado. Você não possui permissão."
            in driver.find_element(By.TAG_NAME, "body").text
        )

        mensagem = navegador.find_element(
            By.TAG_NAME, "body"
        ).text

        assert (
            "Acesso negado. Você não possui permissão."
            in mensagem
        )

        print(
            "APROVADO: funcionário não acessa "
            "o gerenciamento de usuários."
        )

    finally:
        navegador.quit()


def testar_acesso_administrador():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        titulo = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.TAG_NAME, "h2")
            )
        )

        assert titulo.text == "Gerenciar Usuários"

        print(
            "APROVADO: administrador acessa "
            "o gerenciamento de usuários."
        )

    finally:
        navegador.quit()


def testar_id_invalido_edicao():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        ids_invalidos = ["abc", "0", "-1"]

        for identificador in ids_invalidos:
            navegador.get(
                URL_PAPERTECH
                + "modules/usuarios/editar.php?id="
                + identificador
            )

            corpo = WebDriverWait(navegador, 10).until(
                EC.presence_of_element_located(
                    (By.TAG_NAME, "body")
                )
            )

            assert "ID de usuário inválido." in corpo.text, (
                f"O ID {identificador} não foi bloqueado."
            )

        print(
            "APROVADO: IDs inválidos foram rejeitados."
        )

    finally:
        navegador.quit()


def testar_usuario_inexistente_edicao():
    navegador = iniciar_navegador()

    try:
        login_administrador(navegador)

        navegador.get(
            URL_PAPERTECH
            + "modules/usuarios/editar.php?id=999999"
        )

        corpo = WebDriverWait(navegador, 10).until(
            EC.presence_of_element_located(
                (By.TAG_NAME, "body")
            )
        )

        assert "Usuário não encontrado." in corpo.text

        print(
            "APROVADO: ID de usuário inexistente foi rejeitado."
        )

    finally:
        navegador.quit()


if __name__ == "__main__":
    testar_acesso_sem_login()
    testar_acesso_funcionario()
    testar_acesso_administrador()
    testar_id_invalido_edicao()
    testar_usuario_inexistente_edicao()
