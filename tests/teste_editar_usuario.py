
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


def testar_abertura_edicao_usuario():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        link_editar = espera.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, "a[href*='editar.php?id=']")
            )
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        titulo = espera.until(
            EC.visibility_of_element_located(
                (By.TAG_NAME, "h2")
            )
        )

        assert titulo.text == "Editar Usuário"

        formulario = navegador.find_element(
            By.TAG_NAME, "form"
        )

        assert formulario.is_displayed()

        campo_nome = navegador.find_element(By.NAME, "nome")
        campo_email = navegador.find_element(By.NAME, "email")
        campo_tipo = navegador.find_element(By.NAME, "tipo")
        campo_senha = navegador.find_element(By.NAME, "senha")
        campo_confirmar = navegador.find_element(
            By.NAME, "confirmar_senha"
        )

        assert campo_nome.get_attribute("value").strip() != ""
        assert campo_email.get_attribute("value").strip() != ""
        assert campo_tipo.get_attribute("value") in [
            "admin",
            "funcionario"
        ]

        assert campo_senha.get_attribute("value") == ""
        assert campo_confirmar.get_attribute("value") == ""

        print("TESTE APROVADO: abertura e carregamento da edição.")

    finally:
        navegador.quit()


def testar_email_duplicado():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        # Obtém os usuários cadastrados na tabela
        linhas = espera.until(
            EC.presence_of_all_elements_located(
                (By.CSS_SELECTOR, "table tbody tr")
            )
        )

        assert len(linhas) >= 2, (
            "É necessário ter pelo menos dois usuários cadastrados."
        )

        # Utiliza o e-mail de um usuário diferente
        email_duplicado = linhas[1].find_element(
            By.CSS_SELECTOR, "td:nth-child(3)"
        ).text.strip()

        # Abre a edição do primeiro usuário
        link_editar = linhas[0].find_element(
            By.CSS_SELECTOR, "a[href*='editar.php?id=']"
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        campo_email = espera.until(
            EC.presence_of_element_located(
                (By.NAME, "email")
            )
        )

        campo_email.clear()
        campo_email.send_keys(email_duplicado)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        # Verifica a mensagem de e-mail duplicado
        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Este e-mail já está sendo utilizado."
        )

        print("TESTE APROVADO: bloqueio de e-mail duplicado.")

    finally:
        navegador.quit()


def testar_senhas_diferentes():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        link_editar = espera.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, "a[href*='editar.php?id=']")
            )
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        campo_senha = navegador.find_element(
            By.NAME, "senha"
        )

        campo_confirmar = navegador.find_element(
            By.NAME, "confirmar_senha"
        )

        campo_senha.send_keys("SenhaTeste123")
        campo_confirmar.send_keys("SenhaDiferente123")

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == "As senhas não coincidem."

        print(
            "TESTE APROVADO: bloqueio de senhas diferentes."
        )

    finally:
        navegador.quit()


def testar_senha_curta():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        link_editar = espera.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, "a[href*='editar.php?id=']")
            )
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        campo_senha = navegador.find_element(
            By.NAME, "senha"
        )

        campo_confirmar = navegador.find_element(
            By.NAME, "confirmar_senha"
        )

        campo_senha.send_keys("1234567")
        campo_confirmar.send_keys("1234567")

        # Remove temporariamente a validação HTML5
        # para testar a validação feita pelo PHP.
        navegador.execute_script(
            """
            document.querySelector('[name="senha"]').removeAttribute('minlength');
            document.querySelector('[name="confirmar_senha"]').removeAttribute('minlength');
            """
        )

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "A nova senha deve possuir pelo menos 8 caracteres."
        )

        print(
            "TESTE APROVADO: bloqueio de senha com menos de 8 caracteres."
        )

    finally:
        navegador.quit()


def testar_nome_obrigatorio():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        link_editar = espera.until(
            EC.element_to_be_clickable(
                (By.CSS_SELECTOR, "a[href*='editar.php?id=']")
            )
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        campo_nome = navegador.find_element(
            By.NAME, "nome"
        )

        campo_nome.clear()

        # Remove required para testar a validação do PHP
        navegador.execute_script(
            "arguments[0].removeAttribute('required');",
            campo_nome
        )

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Preencha todos os campos obrigatórios."
        )

        print(
            "TESTE APROVADO: validação de nome obrigatório."
        )

    finally:
        navegador.quit()


def abrir_edicao_primeiro_usuario(navegador):
    realizar_login(navegador)

    navegador.get(
        URL_PAPERTECH + "modules/usuarios/index.php"
    )

    espera = WebDriverWait(navegador, 10)

    link_editar = espera.until(
        EC.element_to_be_clickable(
            (By.CSS_SELECTOR, "a[href*='editar.php?id=']")
        )
    )

    link_editar.click()

    espera.until(EC.url_contains("editar.php"))

    return espera


def testar_email_invalido():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "email")

        campo.clear()
        campo.send_keys("email_invalido")

        navegador.execute_script("""
            arguments[0].removeAttribute('required');
            arguments[0].setAttribute('type', 'text');
        """, campo)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == "Informe um e-mail válido."

        print("TESTE APROVADO: bloqueio de e-mail inválido.")

    finally:
        navegador.quit()



def testar_nome_acima_limite():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "nome")

        # Remove a limitação antes de preencher o campo
        navegador.execute_script(
            "arguments[0].removeAttribute('maxlength');",
            campo
        )

        campo.clear()
        campo.send_keys("A" * 151)

        # Confirma que os 151 caracteres foram inseridos
        assert len(campo.get_attribute("value")) == 151

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "O nome deve ter no máximo 150 caracteres."
        )

        print(
            "TESTE APROVADO: bloqueio de nome acima de 150 caracteres."
        )

    finally:
        navegador.quit()




def testar_email_acima_limite():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "email")

        # Remove o limite antes de preencher
        navegador.execute_script(
            "arguments[0].removeAttribute('maxlength');",
            campo
        )

        # Cria um e-mail válido com mais de 150 caracteres
        email_longo = (
            "a" * 64 + "@" +
            "b" * 60 + "." +
            "c" * 30 + ".com"
        )

        campo.clear()
        campo.send_keys(email_longo)

        # Confirma que o campo recebeu mais de 150 caracteres
        valor = campo.get_attribute("value")

        assert len(valor) > 150, (
            f"O e-mail ficou com apenas {len(valor)} caracteres."
        )

        # Confirma que o navegador considera o e-mail válido
        assert navegador.execute_script(
            "return arguments[0].checkValidity();",
            campo
        ), "O navegador considerou o e-mail inválido."

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "O e-mail deve ter no máximo 150 caracteres."
        )

        print(
            "TESTE APROVADO: bloqueio de e-mail acima de 150 caracteres."
        )

    finally:
        navegador.quit()



def testar_email_obrigatorio():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "email")

        campo.clear()

        navegador.execute_script("""
            arguments[0].removeAttribute('required');
        """, campo)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Preencha todos os campos obrigatórios."
        )

        print("TESTE APROVADO: validação de e-mail obrigatório.")

    finally:
        navegador.quit()


def testar_tipo_usuario_invalido():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "tipo")

        navegador.execute_script("""
            arguments[0].removeAttribute('required');

            let opcao = document.createElement('option');
            opcao.value = 'tipo_invalido';
            opcao.text = 'Tipo inválido';

            arguments[0].appendChild(opcao);
            arguments[0].value = 'tipo_invalido';
        """, campo)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Tipo de usuário inválido."
        )

        print("TESTE APROVADO: bloqueio de tipo inválido.")

    finally:
        navegador.quit()


def testar_tipo_usuario_obrigatorio():
    navegador = iniciar_navegador()

    try:
        abrir_edicao_primeiro_usuario(navegador)

        campo = navegador.find_element(By.NAME, "tipo")

        navegador.execute_script("""
            arguments[0].removeAttribute('required');

            let opcao = document.createElement('option');
            opcao.value = '';
            opcao.text = 'Selecione';

            arguments[0].appendChild(opcao);
            arguments[0].value = '';
        """, campo)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Preencha todos os campos obrigatórios."
        )

        print("TESTE APROVADO: validação de tipo obrigatório.")

    finally:
        navegador.quit()


def testar_protecao_proprio_administrador():
    navegador = iniciar_navegador()

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        linhas = espera.until(
            EC.presence_of_all_elements_located(
                (By.CSS_SELECTOR, "table tbody tr")
            )
        )

        linha_propria = None

        for linha in linhas:
            email = linha.find_element(
                By.CSS_SELECTOR, "td:nth-child(3)"
            ).text.strip()

            if email == EMAIL_ADMIN:
                linha_propria = linha
                break

        assert linha_propria is not None, (
            "A conta administrativa não foi encontrada na listagem."
        )

        link_editar = linha_propria.find_element(
            By.CSS_SELECTOR, "a[href*='editar.php?id=']"
        )

        link_editar.click()

        espera.until(EC.url_contains("editar.php"))

        campo_tipo = navegador.find_element(By.NAME, "tipo")

        assert campo_tipo.get_attribute("value") == "admin", (
            "A conta configurada não está como administradora."
        )

        navegador.execute_script("""
            arguments[0].value = 'funcionario';
        """, campo_tipo)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        mensagem = espera.until(
            EC.visibility_of_element_located(
                (By.CSS_SELECTOR, ".alert-danger")
            )
        )

        assert mensagem.text.strip() == (
            "Você não pode remover seu próprio acesso de administrador."
        )

        print(
            "TESTE APROVADO: proteção do próprio administrador."
        )

    finally:
        navegador.quit()


def testar_edicao_usuario_com_sucesso():
    navegador = iniciar_navegador()
    id_usuario = None
    nome_original = None
    alteracao_realizada = False

    try:
        realizar_login(navegador)

        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        espera = WebDriverWait(navegador, 10)

        linhas = espera.until(
            EC.presence_of_all_elements_located(
                (By.CSS_SELECTOR, "table tbody tr")
            )
        )

        # Procura um funcionário, evitando a conta administrativa.
        link_editar = None

        for linha in linhas:
            tipo = linha.find_element(
                By.CSS_SELECTOR, "td:nth-child(4)"
            ).text.strip().lower()

            if "funcionário" in tipo or "funcionario" in tipo:
                link_editar = linha.find_element(
                    By.CSS_SELECTOR,
                    "a[href*='editar.php?id=']"
                )
                break

        assert link_editar is not None, (
            "Nenhum funcionário foi encontrado. "
            "Cadastre um funcionário antes deste teste."
        )

        endereco = link_editar.get_attribute("href")
        id_usuario = endereco.split("id=")[1].split("&")[0]

        navegador.get(
            URL_PAPERTECH
            + "modules/usuarios/editar.php?id="
            + id_usuario
        )

        campo_nome = espera.until(
            EC.presence_of_element_located(
                (By.NAME, "nome")
            )
        )

        nome_original = campo_nome.get_attribute("value")

        assert nome_original.strip() != ""

        # Cria um nome temporário dentro do limite de 150 caracteres.
        nome_teste = nome_original[:140] + " [TESTE]"

        campo_nome.clear()
        campo_nome.send_keys(nome_teste)

        navegador.find_element(
            By.CSS_SELECTOR, "button[type='submit']"
        ).click()

        espera.until(EC.url_contains("editado=1"))
        alteracao_realizada = True

        # Atualiza a página para conferir a persistência.
        navegador.get(
            URL_PAPERTECH + "modules/usuarios/index.php"
        )

        linha_atualizada = espera.until(
            EC.presence_of_element_located(
                (
                    By.XPATH,
                    f"//table/tbody/tr[td[1][normalize-space()='{id_usuario}']]"
                )
            )
        )

        nome_salvo = linha_atualizada.find_element(
            By.CSS_SELECTOR, "td:nth-child(2)"
        ).text.strip()

        assert nome_salvo == nome_teste, (
            f"Nome não persistiu. Esperado: {nome_teste}; "
            f"Encontrado: {nome_salvo}"
        )

        print(
            "TESTE APROVADO: edição realizada e "
            "alteração persistida após atualizar a página."
        )

    finally:
        # Tenta restaurar o nome original, mesmo se uma
        # verificação do teste falhar depois da edição.
        if alteracao_realizada and id_usuario and nome_original:
            try:
                navegador.get(
                    URL_PAPERTECH
                    + "modules/usuarios/editar.php?id="
                    + id_usuario
                )

                espera = WebDriverWait(navegador, 10)

                campo_nome = espera.until(
                    EC.presence_of_element_located(
                        (By.NAME, "nome")
                    )
                )

                campo_nome.clear()
                campo_nome.send_keys(nome_original)

                navegador.find_element(
                    By.CSS_SELECTOR, "button[type='submit']"
                ).click()

                espera.until(EC.url_contains("editado=1"))

                print("Nome original restaurado.")

            except Exception as erro:
                print(
                    "ATENÇÃO: não foi possível restaurar "
                    f"automaticamente o nome original: {erro}"
                )

        navegador.quit()




if __name__ == "__main__":
    """"testar_abertura_edicao_usuario()
    testar_email_duplicado()
    testar_senhas_diferentes()
    testar_senha_curta()
    testar_nome_obrigatorio()
    testar_email_invalido()
    testar_nome_acima_limite()
    testar_email_acima_limite()
    testar_email_obrigatorio()
    testar_tipo_usuario_invalido()
    testar_tipo_usuario_obrigatorio()
    testar_protecao_proprio_administrador()"""
    testar_edicao_usuario_com_sucesso()