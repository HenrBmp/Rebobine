<?php

require_once __DIR__ . '/../autoloader.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Acesso permitido somente pelo terminal.\n");
}

$login = trim((string)($_ENV['REBOBINE_ADMIN_LOGIN'] ?? getenv('REBOBINE_ADMIN_LOGIN') ?? ''));
$senha = (string)($_ENV['REBOBINE_ADMIN_PASSWORD'] ?? getenv('REBOBINE_ADMIN_PASSWORD') ?? '');
$nome = trim((string)($_ENV['REBOBINE_ADMIN_NAME'] ?? getenv('REBOBINE_ADMIN_NAME') ?? 'Administrador'));
$email = trim((string)($_ENV['REBOBINE_ADMIN_EMAIL'] ?? getenv('REBOBINE_ADMIN_EMAIL') ?? ''));

if (!preg_match('/^[a-zA-Z0-9._-]{3,64}$/', $login)) {
    fwrite(STDERR, "Defina REBOBINE_ADMIN_LOGIN com 3 a 64 caracteres válidos.\n");
    exit(1);
}

if (strlen($senha) < 10) {
    fwrite(STDERR, "Defina REBOBINE_ADMIN_PASSWORD com pelo menos 10 caracteres.\n");
    exit(1);
}

if ($nome === '' || strlen($nome) > 255) {
    fwrite(STDERR, "REBOBINE_ADMIN_NAME deve conter entre 1 e 255 caracteres.\n");
    exit(1);
}

if ($email === '' || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Defina REBOBINE_ADMIN_EMAIL com um endereço válido.\n");
    exit(1);
}

try {
    $db = \model\Database::usandoEnv();

    $dominio = $db->query(
        "SELECT Id, Tipo, Ativo
         FROM Dominio
         WHERE Id = 7
         LIMIT 1"
    )->fetch(\PDO::FETCH_ASSOC);

    if (
        !$dominio ||
        (int)$dominio['Tipo'] !== 3 ||
        (int)$dominio['Ativo'] !== 1
    ) {
        fwrite(STDERR, "Erro: o domínio Administrador não está configurado corretamente.\n");
        exit(1);
    }

    $existente = $db->query(
        "SELECT Id FROM Usuario
         WHERE Login = :login OR Email = :email
         LIMIT 1",
        ['login' => $login, 'email' => $email]
    )->fetch(\PDO::FETCH_ASSOC);

    if ($existente) {
        fwrite(STDERR, "A conta inicial já existe ou usa o mesmo login/e-mail. Nenhuma alteração foi feita.\n");
        exit(1);
    }

    $stmt = $db->queryComTransacao(
        "INSERT INTO Usuario
         (Nome, Login, Senha, Email, IdTipo, TrocarSenha, Ativo)
         VALUES
         (:nome, :login, :senha, :email, 7, 1, 1)",
        [
            'nome' => $nome,
            'login' => $login,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'email' => $email
        ]
    );

    if ($stmt->rowCount() !== 1) {
        fwrite(STDERR, "Não foi possível confirmar a criação da conta.\n");
        exit(1);
    }

    echo "Administrador inicial criado com sucesso.\n";
    echo "A troca de senha é obrigatória no primeiro acesso.\n";
} catch (\Throwable $e) {
    error_log('[REBOBINE][SeedAdmin] ' . $e->getMessage());
    fwrite(STDERR, "Erro ao criar administrador. Consulte o log do PHP.\n");
    exit(1);
}
