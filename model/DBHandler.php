<?php
function loadEnv(string $path)
{
    $env = parse_ini_file($path);
    foreach ($env as $key => $value) $_ENV[$key] = $value;
}

class Database
{
    static PDO $conn;

    public function __construct(string $usuario, string $senha, string $schema, string $host = 'localhost', int $porta = 3306)
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$host;port=$porta;dbname=$schema;",
                username: $usuario,
                password: $senha
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            die("Erro de conexão " . $err->getCode());
        }
    }

    public static function fromEnv(): Database
    {
        loadEnv(__DIR__ . "/.env");

        $usuario = $_ENV["DB_USER"];
        $senha   = $_ENV["DB_PASSWD"];
        $schema  = $_ENV["DB_NAME"];
        $host    = $_ENV["DB_HOST"] ?? "localhost";
        $porta   = $_ENV["DB_PORT"] ?? 3306;

        if (!isset($usuario) || !isset($senha) || !isset($schema)) die("Variáveis de ambiente ausentes.");

        return new Database($usuario, $senha, $schema, $host, $porta);
    }

    /**
     * Executa uma query sem transações. Apenas para consultas simples.
     * @param string $sql - Query SQL com placeholders para os parâmetros.
     * @param array<string,string|int|double> $params - Array associativo com os parâmetros, opcional.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->conn->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    /**
     * Executa uma query iniciando uma transação.
     * @param string $sql - Query SQL com placeholders para os parâmetros.
     * @param array<string,string|int> $params - Array associativo com os parâmetros, opcional.
     */
    public function transactionQuery(string $sql, array $params = []): PDOStatement
    {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->query($sql, $params);
            $this->conn->commit();

            return $stmt;
        } catch (PDOException $err) {
            $this->conn->rollBack();
            throw $err;
        }
    }
}
