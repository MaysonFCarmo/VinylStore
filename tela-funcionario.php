
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinylStore - Funcionários</title>
</head>
<body>

<h1>VinylStore</h1>
<h2>Área do Funcionário</h2>

<a href="index.php">Voltar à loja</a>

<hr>

<!-- Mensagens do sistema -->
<?php if ($erro !== ''): ?>
    <p style="color:red;">
        <?= htmlspecialchars($erro) ?>
    </p>
<?php endif; ?>

<?php if ($mensagem !== ''): ?>
    <p style="color:green;">
        <?= htmlspecialchars($mensagem) ?>
    </p>
<?php endif; ?>

<!-- Funcionário autenticado -->
<?php if ($logado): ?>

    <h3>
        Olá, <?= htmlspecialchars($_SESSION['funcionario']) ?>!
    </h3>

    <p>Você está conectado ao sistema.</p>

    <a href="cadastro.php">Gerenciar Discos</a>
    <br><br>
    <a href="funcionario.php?sair=1">Sair</a>

<?php elseif (contarFuncionarios() > 0): ?>

    <!-- Formulário de login -->
    <h3>Entrar</h3>

    <form action="funcionario.php" method="POST">

        <input type="hidden" name="acao" value="entrar">

        <p>
            <label>E-mail:</label><br>
            <input type="email" name="email" required>
        </p>

        <p>
            <label>Senha:</label><br>
            <input type="password" name="senha" required>
        </p>

        <button type="submit">Entrar</button>

    </form>

<?php endif; ?>

<!-- Cadastro permitido somente quando autorizado -->
<?php if ($podeCadastrar): ?>

    <hr>

    <h3>
        <?= contarFuncionarios() === 0
            ? 'Cadastrar primeiro funcionário'
            : 'Cadastrar novo funcionário' ?>
    </h3>

    <form action="funcionario.php" method="POST">

        <input type="hidden" name="acao" value="cadastrar">

        <p>
            <label>Nome:</label><br>
            <input type="text" name="nome" required>
        </p>

        <p>
            <label>E-mail:</label><br>
            <input type="email" name="email" required>
        </p>

        <p>
    <label>Senha:</label><br>
    <input type="password"
           name="senha"
           minlength="8"
           required>
</p>

        <button type="submit">
            Cadastrar Funcionário
        </button>

    </form>

<?php endif; ?>

</body>
</html>
