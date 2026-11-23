
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from tests.config_local import (
    EMAIL_ADMIN, SENHA_ADMIN,
    EMAIL_FUNCIONARIO, SENHA_FUNCIONARIO
)

URL_PAPERTECH = "http://localhost/PaperTech/"


def iniciar_navegador():
    navegador = webdriver.Chrome()
    navegador.maximize_window()
    return navegador



def realizar_login(navegador, email, senha):
    navegador.get(URL_PAPERTECH)

    espera = WebDriverWait(navegador, 15)

    espera.until(
        EC.presence_of_element_located((By.NAME, "email"))
    ).send_keys(email)

    navegador.find_element(
        By.NAME, "senha"
    ).send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR, "button[type='submit']"
    ).click()

    try:
        espera.until(
            lambda driver:
            "dashboard.php" in driver.current_url
            or "E-mail ou senha incorretos."
            in driver.find_element(By.TAG_NAME, "body").text
        )

    except Exception:
        corpo = navegador.find_element(
            By.TAG_NAME, "body"
        ).text

        raise RuntimeError(
            "Não foi possível concluir o login.\n"
            f"URL atual: {navegador.current_url}\n"
            f"Conteúdo da página: {corpo[:1000]}"
        )

    if "dashboard.php" not in navegador.current_url:
        raise RuntimeError(
            "O sistema recusou o login.\n"
            f"URL atual: {navegador.current_url}\n"
            f"Mensagem: {navegador.find_element(By.TAG_NAME, 'body').text}"
        )


def login_administrador(navegador):
    realizar_login(navegador, EMAIL_ADMIN, SENHA_ADMIN)


def login_funcionario(navegador):
    realizar_login(
        navegador,
        EMAIL_FUNCIONARIO,
        SENHA_FUNCIONARIO
    )


def localizar_linha_por_email(navegador, email):
    linhas = navegador.find_elements(
        By.CSS_SELECTOR, "table tbody tr"
    )

    for linha in linhas:
        email_linha = linha.find_element(
            By.CSS_SELECTOR, "td:nth-child(3)"
        ).text.strip()

        if email_linha == email:
            return linha

    return None
