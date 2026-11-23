
import subprocess
import sys

TESTES = [
    "tests.teste_login",
    "tests.teste_editar_usuario",
    "tests.teste_acesso_usuarios",
    "tests.teste_csrf",
    "tests.teste_fase_final",
    "tests.teste_excluir_usuario",
]

resultados = []

print("\n====================================")
print("       TESTES AUTOMATIZADOS")
print("             PAPERTECH")
print("====================================\n")

for teste in TESTES:

    print(f"\nExecutando: {teste}")
    print("-" * 45)

    try:
        resultado = subprocess.run(
            [sys.executable, "-m", teste],
            cwd=".",
            timeout=600
        )

        aprovado = resultado.returncode == 0

    except subprocess.TimeoutExpired:
        print("ERRO: tempo limite de execução excedido.")
        aprovado = False

    resultados.append((teste, aprovado))

    print(
        "RESULTADO:",
        "APROVADO" if aprovado else "REPROVADO"
    )

print("\n====================================")
print("          RESUMO FINAL")
print("====================================")

for teste, aprovado in resultados:
    status = "APROVADO" if aprovado else "REPROVADO"
    print(f"{teste}: {status}")

total = len(resultados)
aprovados = sum(1 for _, status in resultados if status)

print(f"\nTotal: {total}")
print(f"Aprovados: {aprovados}")
print(f"Reprovados: {total - aprovados}")

sys.exit(0 if aprovados == total else 1)
