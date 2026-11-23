
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


def realizar_login(navegador):

    navegador.get(URL_PAPERTECH)

    espera = WebDriverWait(navegador, 10)

    espera.until(
        EC.presence_of_element_located((By.NAME, "email"))
    ).send_keys(EMAIL_ADMIN)

    navegador.find_element(
        By.NAME, "senha"
    ).send_keys(SENHA_ADMIN)

    navegador.find_element(
        By.CSS_SELECTOR, "button[type='submit']"
    ).click()

    espera.until(EC.url_contains("dashboard.php"))


def testar_listagem_usuarios():

    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            "http://localhost/PaperTech/modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        titulo = espera.until(
            EC.visibility_of_element_located(
                (By.TAG_NAME, "h2")
            )
        )

        assert titulo.text == "Gerenciar Usuários"

        tabela = navegador.find_element(
            By.CSS_SELECTOR, "table"
        )

        assert tabela.is_displayed()

        cabecalhos = navegador.find_elements(
            By.CSS_SELECTOR, "table thead th"
        )

        assert len(cabecalhos) == 6

        nomes_cabecalhos = [
            cabecalho.text for cabecalho in cabecalhos
        ]

        assert "Nome" in nomes_cabecalhos
        assert "E-mail" in nomes_cabecalhos
        assert "Tipo" in nomes_cabecalhos
        assert "Ações" in nomes_cabecalhos

        print("TESTE APROVADO: listagem de usuários.")

    finally:
        navegador.quit()


if __name__ == "__main__":
    testar_listagem_usuarios()
