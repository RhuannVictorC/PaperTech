
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from tests.config_local import (
    EMAIL_ADMIN, SENHA_ADMIN
)


URL_PAPERTECH = "http://localhost/PaperTech/"


def iniciar_navegador():
    navegador = webdriver.Chrome()
    navegador.maximize_window()
    return navegador


def testar_login_valido():

    navegador = iniciar_navegador()

    try:
        navegador.get(URL_PAPERTECH)

        espera = WebDriverWait(navegador, 10)

        campo_email = espera.until(
            EC.presence_of_element_located((By.NAME, "email"))
        )

        campo_senha = navegador.find_element(By.NAME, "senha")

        campo_email.send_keys(EMAIL_ADMIN)
        campo_senha.send_keys(SENHA_ADMIN)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        espera.until(EC.url_contains("dashboard.php"))

        assert "dashboard.php" in navegador.current_url

        print("TESTE APROVADO: login válido.")

    finally:
        navegador.quit()


def testar_senha_incorreta():

    navegador = iniciar_navegador()

    try:
        navegador.get(URL_PAPERTECH)

        espera = WebDriverWait(navegador, 10)

        navegador.find_element(
            By.NAME, "email"
        ).send_keys(EMAIL_ADMIN)

        navegador.find_element(
            By.NAME, "senha"
        ).send_keys("SenhaIncorreta123!")

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CLASS_NAME, "alert-danger")
            )
        )

        assert mensagem.text == "E-mail ou senha incorretos."

        print("TESTE APROVADO: senha incorreta.")

    finally:
        navegador.quit()


def testar_email_vazio():

    navegador = iniciar_navegador()

    try:
        navegador.get(URL_PAPERTECH)

        campo_email = navegador.find_element(By.NAME, "email")

        campo_email.clear()

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        # Verifica a validação nativa do navegador
        email_valido = navegador.execute_script(
            "return arguments[0].checkValidity();",
            campo_email
        )

        assert email_valido is False

        print("TESTE APROVADO: e-mail vazio bloqueado.")

    finally:
        navegador.quit()


def testar_senha_vazia():

    navegador = iniciar_navegador()

    try:
        navegador.get(URL_PAPERTECH)

        navegador.find_element(
            By.NAME, "email"
        ).send_keys(EMAIL_ADMIN)

        campo_senha = navegador.find_element(By.NAME, "senha")

        campo_senha.clear()

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        senha_valida = navegador.execute_script(
            "return arguments[0].checkValidity();",
            campo_senha
        )

        assert senha_valida is False

        print("TESTE APROVADO: senha vazia bloqueada.")

    finally:
        navegador.quit()


if __name__ == "__main__":

    testar_login_valido()
    testar_senha_incorreta()
    testar_email_vazio()
    testar_senha_vazia()

    print("\nTODOS OS TESTES DE LOGIN FORAM EXECUTADOS.")
