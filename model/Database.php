<?php

namespace model;

use PDO, PDOException, PDOStatement;

class Database
{
    private static Database $instancia;
    public PDO $conn;

    private function __construct(string $usuario, string $senha, string $schema, string $host = 'localhost', int $porta = 3306)
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

    public static function usandoEnv(): Database
    {
        if (isset(self::$instancia)) {
            return self::$instancia;
        }

        self::carregarArquivoEnv("../.env");

        if (!isset($_ENV["DB_USER"]) || !isset($_ENV["DB_PASSWD"]) || !isset($_ENV["DB_NAME"])) {
            die("Variáveis de ambiente ausentes.");
        }

        $usuario = $_ENV["DB_USER"];
        $senha   = $_ENV["DB_PASSWD"];
        $schema  = $_ENV["DB_NAME"];
        $host    = $_ENV["DB_HOST"] ?? "localhost";
        $porta   = $_ENV["DB_PORT"] ?? 3306;

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
        if (file_exists($path)) {
            $env = parse_ini_file($path);
            foreach ($env as $key => $value) $_ENV[$key] = $value;
        }
    }
}
