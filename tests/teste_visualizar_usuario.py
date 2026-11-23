
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


def testar_visualizacao_usuario():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        # Abre a listagem de usuários
        navegador.get(
            "http://localhost/PaperTech/modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        # Localiza o primeiro link de visualização
        link_visualizar = espera.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, "a[href*='visualizar.php?id=']")
            )
        )

        link_visualizar.click()

        # Aguarda a página de visualização
        espera.until(EC.url_contains("visualizar.php"))

        # Verifica o título
        titulo = espera.until(
            EC.visibility_of_element_located(
                (By.TAG_NAME, "h2")
            )
        )

        assert titulo.text == "Visualizar Usuário"

        # Verifica se os dados do usuário estão apresentados
        corpo = navegador.find_element(
            By.CLASS_NAME, "card-body"
        )

        assert corpo.is_displayed()

        texto = corpo.text

        assert "ID:" in texto
        assert "Nome:" in texto
        assert "E-mail:" in texto
        assert "Tipo de usuário:" in texto
        assert "Data de cadastro:" in texto

        # Verifica se existe o botão Voltar
        botao_voltar = navegador.find_element(
            By.LINK_TEXT, "Voltar"
        )

        assert botao_voltar.is_displayed()

        print(
            "TESTE APROVADO: visualização dos dados do usuário."
        )

    finally:
        navegador.quit()


if __name__ == "__main__":
    testar_visualizacao_usuario()

    
