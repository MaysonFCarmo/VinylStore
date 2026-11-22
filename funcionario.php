
<?php
// Inicia a sessão antes de qualquer HTML.
session_start();

// Conecta ao banco SQLite.
require __DIR__ . '/banco.php';

// Mensagens exibidas na tela.
$erro = '';
$mensagem = '';

// Verifica se existe um funcionário autenticado.
$logado = !empty($_SESSION['funcionario_id']);

// Permite cadastrar o primeiro funcionário.
// Depois, somente funcionários logados podem cadastrar.
$podeCadastrar = contarFuncionarios() === 0 || $logado;


// --------------------------------------------------
// LOGOUT
// --------------------------------------------------
if (isset($_GET['sair'])) {

    // Limpa os dados da sessão.
    $_SESSION = [];

    // Remove o cookie da sessão.
    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Encerra a sessão.
    session_destroy();

    header('Location: funcionario.php');
    exit;
}


// --------------------------------------------------
// PROCESSAMENTO DOS FORMULÁRIOS
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Identifica qual formulário foi enviado.
    $acao = $_POST['acao'] ?? '';


    // ----------------------------------------------
    // CADASTRO DE FUNCIONÁRIO
    // ----------------------------------------------
    if ($acao === 'cadastrar') {

        // Confere a permissão de cadastro.
        $podeCadastrar = contarFuncionarios() === 0 ||
                         !empty($_SESSION['funcionario_id']);

        if (!$podeCadastrar) {
            header('Location: funcionario.php');
            exit;
        }

        // Recebe os dados do formulário.
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        // Valida os dados.
        if ($nome === '') {

            $erro = 'Informe o nome.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $erro = 'Informe um e-mail válido.';

        } elseif (strlen($senha) < 8) {

            $erro = 'A senha deve ter pelo menos 8 caracteres.';

        } elseif (buscarFuncionario($email)) {

            $erro = 'Este e-mail já está cadastrado.';

        } else {

            // Cadastra o funcionário.
            try {

                inserirFuncionario($nome, $email, $senha);

                $mensagem = 'Funcionário cadastrado com sucesso!';

            } catch (PDOException $e) {

                $erro = 'Não foi possível cadastrar o funcionário.';
            }
        }

    }


    // ----------------------------------------------
    // LOGIN
    // ----------------------------------------------
    elseif ($acao === 'entrar') {

        // Recebe e-mail e senha.
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        // Busca o funcionário no banco.
        $funcionario = buscarFuncionario($email);

        // Confere a senha armazenada como hash.
        if (
            $funcionario &&
            password_verify($senha, $funcionario['senha'])
        ) {

            // Renova o identificador da sessão.
            session_regenerate_id(true);

            // Guarda os dados do funcionário na sessão.
            $_SESSION['funcionario_id'] = $funcionario['id'];
            $_SESSION['funcionario'] = $funcionario['nome'];

            // Redireciona para a administração.
            header('Location: cadastro.php');
            exit;

        } else {

            $erro = 'E-mail ou senha errados.';
        }
    }
}


// --------------------------------------------------
// ATUALIZA AS PERMISSÕES
// --------------------------------------------------
$logado = !empty($_SESSION['funcionario_id']);

$podeCadastrar = contarFuncionarios() === 0 || $logado;


// --------------------------------------------------
// EXIBE A TELA DE FUNCIONÁRIOS
// --------------------------------------------------
require __DIR__ . '/tela-funcionario.php';
?>
