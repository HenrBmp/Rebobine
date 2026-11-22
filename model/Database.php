<?php

namespace model;

use PDO, PDOException, PDOStatement;

class Database
{
    private static Database $instancia;
    private PDO $conn;

    private function __construct(string $usuario, string $senha, string $schema, string $host = 'localhost', int $porta = 3306)
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$host;port=$porta;dbname=$schema;charset=utf8mb4;",
                username: $usuario,
                password: $senha
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            error_log("[REBOBINE][Database] Falha na conexão PDO: " . $err->getMessage());
            die("Não foi possível conectar ao banco de dados. Consulte o log do PHP/Apache.");
        }
    }

    public static function usandoEnv(): Database
    {
        if (isset(self::$instancia)) {
            return self::$instancia;
        }

        self::carregarArquivoEnv(dirname(__DIR__) . DIRECTORY_SEPARATOR . ".env");

        if (!isset($_ENV["DB_USER"]) || !isset($_ENV["DB_PASSWD"]) || !isset($_ENV["DB_NAME"])) {
            die("Variáveis de ambiente ausentes.");
        }

        $usuario = $_ENV["DB_USER"];
        $senha   = $_ENV["DB_PASSWD"];
        $schema  = $_ENV["DB_NAME"];
        $host = trim((string)($_ENV["DB_HOST"] ?? "localhost"));
        $host = trim((string)preg_replace('/\s+[;#].*$/', '', $host));
        if ($host === "") {
            $host = "localhost";
        }

        $portaConfigurada = trim((string)($_ENV["DB_PORT"] ?? ""));
        $portaConfigurada = trim((string)preg_replace('/\s+[;#].*$/', '', $portaConfigurada));
        $porta = $portaConfigurada === "" ? 3306 : filter_var($portaConfigurada, FILTER_VALIDATE_INT);
        if ($porta === false || $porta < 1 || $porta > 65535) {
            die("A variável DB_PORT deve conter uma porta numérica válida.");
        }

        $instancia = new Database($usuario, $senha, $schema, $host, $porta);

        self::$instancia = $instancia;
        return $instancia;
    }

    public static function usandoArgs(
        string $usuario,
        string $senha,
        string $schema,
        string $host = 'localhost',
        int $porta = 3306
    ): Database {
        if (isset(self::$instancia)) return self::$instancia;

        $instancia = new Database($usuario, $senha, $schema, $host, $porta);

        self::$instancia = $instancia;
        return $instancia;
    }

    /**
     * Executa uma query sem transações. Apenas para consultas simples.
     * @param string $sql - Query SQL com placeholders para os parâmetros.
     * @param array<string,string|int|double> $params - Array associativo com os parâmetros, opcional.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->conn->prepare($sql);
        foreach ($params as $nome => $valor) {
            $tipo = \is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $statement->bindValue($nome, $valor, $tipo);
        }
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement;
    }

    /**
     * Executa uma query iniciando uma transação.
     * @param string $sql - Query SQL com placeholders para os parâmetros.
     * @param array<string,string|int> $params - Array associativo com os parâmetros, opcional.
     */
    public function queryComTransacao(string $sql, array $params = []): PDOStatement
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

    private static function carregarArquivoEnv(string $path)
    {
        if (!is_file($path) || !is_readable($path)) {
            error_log("[REBOBINE][Database] Arquivo .env não encontrado ou sem permissão: " . $path);
            return;
        }

        $env = parse_ini_file($path, false, INI_SCANNER_RAW);
        if ($env === false) {
            error_log("[REBOBINE][Database] Não foi possível interpretar o arquivo .env.");
            return;
        }

        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}
