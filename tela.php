<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinylStore - Gerenciar Discos</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>VinylStore - Loja de Discos</h1>

    <a href="index.php">Voltar ao início</a>

    <hr>

    <!-- Identifica o funcionário autenticado -->
    <?php if ($logado): ?>

        <p>
            Funcionário:
            <?= htmlspecialchars($_SESSION['funcionario']) ?>
        </p>

        <a href="funcionario.php?sair=1">Sair</a>

    <?php else: ?>

        <a href="funcionario.php">Área do Funcionário</a>

    <?php endif; ?>

    <hr>

    <!-- Formulário visível somente para funcionários -->
    <?php if ($logado): ?>

        <h2>
            <?= $disco && !empty($disco['id'])
                ? 'Editar Disco' : 'Cadastrar Disco' ?>
        </h2>

        <!-- Exibe mensagens de erro -->
        <?php if (!empty($erro)): ?>
            <p style="color:red;">
                <?= htmlspecialchars($erro) ?>
            </p>
        <?php endif; ?>

        <!-- Formulário de cadastro e edição -->
        <form action="cadastro.php"
            method="POST"
            enctype="multipart/form-data">

            <input type="hidden"
                name="id"
                value="<?= $disco['id'] ?? '' ?>">

            <p>
                <label>Título do álbum:</label><br>

                <input type="text"
                    name="titulo"
                    required
                    value="<?= htmlspecialchars($disco['titulo'] ?? '') ?>">
            </p>

            <p>
                <label>Artista:</label><br>

                <input type="text"
                    name="artista"
                    required
                    value="<?= htmlspecialchars($disco['artista'] ?? '') ?>">
            </p>

            <p>
                <label>Gênero musical:</label><br>

                <input type="text"
                    name="genero"
                    required
                    value="<?= htmlspecialchars($disco['genero'] ?? '') ?>">
            </p>

            <p>
                <label>Preço (R$):</label><br>

                <input type="number"
                    name="preco"
                    step="0.01"
                    min="0.01"
                    required
                    value="<?= htmlspecialchars((string) ($disco['preco'] ?? '')) ?>">
            </p>


            <!-- Exibe a capa atual durante a edição -->
            <?php if (!empty($disco['imagem'])): ?>

                <p>Foto atual:</p>

                <img
                    src="uploads/<?= rawurlencode($disco['imagem']) ?>"
                    width="120"
                    alt="Capa atual do disco">

            <?php endif; ?>

            <!-- Campo para enviar ou trocar a capa -->
            <p>
                <label>
                    <?= !empty($disco['imagem'])
                        ? 'Trocar a capa (opcional):'
                        : 'Capa do disco (opcional):' ?>
                </label><br>

                <input type="file"
                    name="imagem"
                    accept=".jpg,.jpeg,.png,.webp">
            </p>

            <button type="submit">
                <?= $disco && !empty($disco['id'])
                    ? 'Salvar Alterações'
                    : 'Cadastrar Disco' ?>
            </button>

            <?php if ($disco && !empty($disco['id'])): ?>

                <a href="cadastro.php">
                    Cancelar edição
                </a>

            <?php endif; ?>

        </form>

        <hr>

    <?php endif; ?>


    <!-- Catálogo visível para todos -->
    <h2>Discos Cadastrados (<?= count($discos) ?>)</h2>

    <?php if (empty($discos)): ?>

        <p>Nenhum disco cadastrado.</p>

    <?php else: ?>

        <div class="catalogo">

            <?php foreach ($discos as $item): ?>

                <div class="disco-card">

                    <!-- Capa do álbum -->
                    <?php if (!empty($item['imagem'])): ?>

                        <img
                            src="uploads/<?= rawurlencode($item['imagem']) ?>"
                            width="100"
                            alt="Capa do álbum">

                    <?php else: ?>

                        <p>Sem foto</p>

                    <?php endif; ?>

                    <!-- Informações do disco -->
                    <p>
                        <strong>
                            <?= htmlspecialchars($item['titulo']) ?>
                        </strong>

                        - <?= htmlspecialchars($item['artista']) ?>

                        (<?= htmlspecialchars($item['genero']) ?>)

                        - R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                    </p>

                    <!-- Ações disponíveis para funcionários -->
                    <?php if ($logado): ?>

                        <div>
                            <a href="cadastro.php?editar=<?= $item['id'] ?>">
                                Editar
                            </a>

                            <form
                                action="cadastro.php"
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Excluir este disco?');">

                                <button
                                    type="submit"
                                    name="excluir"
                                    value="<?= $item['id'] ?>">
                                    Excluir
                                </button>

                            </form>
                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</body>

</html>