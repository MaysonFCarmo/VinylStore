

<?php
// Inicia a sessão.
session_start();

require __DIR__ . '/banco.php';

// Verifica se existe funcionário autenticado.
$logado = !empty($_SESSION['funcionario_id']);

// Visitantes não podem enviar alterações ou editar discos.
if (!$logado &&
    ($_SERVER['REQUEST_METHOD'] === 'POST' ||
     isset($_GET['editar']))) {

    header('Location: funcionario.php');
    exit;
}

// Pasta onde as imagens serão armazenadas.
$pastaUploads = __DIR__ . '/uploads';

// Verifica a imagem recebida.
function conferirImagem($arquivo) {
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        return "Erro ao enviar a imagem.";
    }

    // Limite de 2 MB.
    if ($arquivo['size'] > 2 * 1024 * 1024) {
        return "A imagem deve ter no máximo 2 MB.";
    }

    // Verifica o conteúdo real da imagem.
    $info = @getimagesize($arquivo['tmp_name']);

    if ($info === false) {
        return "O arquivo enviado não é uma imagem válida.";
    }

    $tipos = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_WEBP
    ];

    if (!in_array($info[2], $tipos, true)) {
        return "Use somente imagens JPG, PNG ou WEBP.";
    }

    return "";
}

// Salva uma imagem com nome aleatório.
function guardarImagem($arquivo) {
    global $pastaUploads;

    $info = getimagesize($arquivo['tmp_name']);

    $extensoes = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp'
    ];

    $extensao = $extensoes[$info[2]];

    // Gera um nome novo, sem usar o nome original.
    $nome = bin2hex(random_bytes(8)) . '.' . $extensao;

    if (!is_dir($pastaUploads)) {
        if (!mkdir($pastaUploads, 0755, true)) {
            throw new RuntimeException("Não foi possível criar a pasta uploads.");
        }
    }

    $destino = $pastaUploads . '/' . $nome;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        throw new RuntimeException("Não foi possível salvar a imagem.");
    }

    return $nome;
}

// Remove uma imagem da pasta uploads.
function apagarImagem($nome) {
    global $pastaUploads;

    if (empty($nome)) {
        return;
    }

    // Impede que o nome aponte para outra pasta.
    $caminho = $pastaUploads . '/' . basename($nome);

    if (is_file($caminho)) {
        unlink($caminho);
    }
}

$erro = "";
$disco = null;

// Processa os formulários.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Excluir disco e imagem.
    if (isset($_POST['excluir'])) {
        $id = (int) $_POST['excluir'];
        $discoExcluir = buscarDisco($id);

        if ($discoExcluir) {
            excluirDisco($id);
            apagarImagem($discoExcluir['imagem']);
        }

        header('Location: cadastro.php');
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $artista = trim($_POST['artista'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $preco = (float) ($_POST['preco'] ?? 0);

    // Mantém os dados preenchidos caso haja erro.
    $disco = [
        'id' => $id,
        'titulo' => $titulo,
        'artista' => $artista,
        'genero' => $genero,
        'preco' => $_POST['preco'] ?? '',
        'imagem' => ''
    ];

    $discoAnterior = $id > 0 ? buscarDisco($id) : null;

    if ($id > 0 && !$discoAnterior) {
        $erro = "Disco não encontrado.";
    }

    $imagem = $discoAnterior['imagem'] ?? null;
    $disco['imagem'] = $imagem;

    if ($titulo === '' || $artista === '' ||
        $genero === '' || $preco <= 0) {
        $erro = "Preencha todos os campos corretamente.";
    }

    $arquivo = $_FILES['imagem'] ?? null;
    $temNovaImagem = $arquivo &&
        $arquivo['error'] !== UPLOAD_ERR_NO_FILE;

    if ($erro === '' && $temNovaImagem) {
        $erro = conferirImagem($arquivo);
    }

    if ($erro === '') {
        $novaImagem = null;

        try {
            // Salva uma nova imagem, se enviada.
            if ($temNovaImagem) {
                $novaImagem = guardarImagem($arquivo);
                $imagem = $novaImagem;
            }

            if ($id > 0) {
                atualizarDisco(
                    $id, $titulo, $artista,
                    $genero, $preco, $imagem
                );
            } else {
                inserirDisco(
                    $titulo, $artista,
                    $genero, $preco, $imagem
                );
            }

            // Apaga a imagem antiga somente após atualizar.
            if ($id > 0 && $novaImagem !== null) {
                apagarImagem($discoAnterior['imagem']);
            }

            header('Location: cadastro.php');
            exit;

        } catch (Exception $e) {
            // Remove o novo arquivo caso o cadastro falhe.
            if ($novaImagem !== null) {
                apagarImagem($novaImagem);
            }

            $erro = "Não foi possível salvar o disco.";
        }
    }
}

// Carrega o disco selecionado para edição.
if ($_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['editar'])) {
    $disco = buscarDisco((int) $_GET['editar']);
}

// Lista os discos.
$discos = listarDiscos();

require __DIR__ . '/tela.php';
?>
