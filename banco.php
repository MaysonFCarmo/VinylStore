
<?php
// Conecta ao banco de dados SQLite.
// Se o arquivo não existir, o SQLite cria automaticamente.
$pdo = new PDO('sqlite:' . __DIR__ . '/vinylstore.sqlite');

// Exibe erros do banco durante o desenvolvimento.
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Cria a tabela de discos, caso ainda não exista.
$pdo->exec("
    CREATE TABLE IF NOT EXISTS discos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        artista TEXT NOT NULL,
        genero TEXT NOT NULL,
        preco REAL NOT NULL
    )
");

// CREATE: cadastra um novo disco.
function inserirDisco($titulo, $artista, $genero, $preco) {
    global $pdo;

    $sql = "INSERT INTO discos (titulo, artista, genero, preco)
            VALUES (:titulo, :artista, :genero, :preco)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':titulo' => $titulo,
        ':artista' => $artista,
        ':genero' => $genero,
        ':preco' => $preco
    ]);
}

// READ: lista todos os discos em ordem alfabética.
function listarDiscos() {
    global $pdo;

    $sql = "SELECT * FROM discos ORDER BY titulo";

    $stmt = $pdo->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// READ: busca um disco pelo ID.
function buscarDisco($id) {
    global $pdo;

    $sql = "SELECT * FROM discos WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// UPDATE: altera os dados e a imagem do disco.
function atualizarDisco($id, $titulo, $artista, $genero, $preco, $imagem) {
    global $pdo;

    // Atualiza todas as informações do disco.
    $sql = "UPDATE discos
            SET titulo = :titulo,
                artista = :artista,
                genero = :genero,
                preco = :preco,
                imagem = :imagem
            WHERE id = :id";

    // Prepara a consulta SQL.
    $stmt = $pdo->prepare($sql);

    // Executa a atualização.
    $stmt->execute([
        ':id' => $id,
        ':titulo' => $titulo,
        ':artista' => $artista,
        ':genero' => $genero,
        ':preco' => $preco,
        ':imagem' => $imagem
    ]);
}

// DELETE: exclui um disco pelo ID.
function excluirDisco($id) {
    global $pdo;

    $sql = "DELETE FROM discos WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

// Tabela de funcionários.
// A senha será armazenada como hash.
$pdo->exec("
    CREATE TABLE IF NOT EXISTS funcionarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL
    )
");

// Conta quantos funcionários existem.
function contarFuncionarios() {
    global $pdo;

    return (int) $pdo->query(
        "SELECT COUNT(*) FROM funcionarios"
    )->fetchColumn();
}

// Busca funcionário pelo e-mail.
function buscarFuncionario($email) {
    global $pdo;

    $sql = "SELECT * FROM funcionarios
            WHERE email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Cadastra um novo funcionário.
function inserirFuncionario($nome, $email, $senha) {
    global $pdo;

    // Transforma a senha em hash.
    $hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO funcionarios
            (nome, email, senha)
            VALUES (:nome, :email, :senha)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $hash
    ]);
}
?>


