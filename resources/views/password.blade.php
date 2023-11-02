<!DOCTYPE html>
<html>
<head>
    <title>Formulário de Mudança de Senha</title>
</head>
<body>
    <h2>Mudança de Senha</h2>
    <form action="processar_mudanca_senha.php" method="post">
        <label for="senha_atual">Senha Atual:</label>
        <input type="password" id="senha_atual" name="senha_atual" required><br><br>

        <label for="nova_senha">Nova Senha:</label>
        <input type="password" id="nova_senha" name="nova_senha" required><br><br>

        <label for="confirmar_nova_senha">Confirmar Nova Senha:</label>
        <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" required><br><br>

        <input type="submit" value="Mudar Senha">
    </form>
</body>
</html>
