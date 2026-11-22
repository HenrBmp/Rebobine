<?php

namespace model;

use PDO;
use PDOException;

/** Persistência exclusiva das contas administrativas. */
class AdminRepository
{
    public static function autenticar(string $login, string $senha): array|false
    {
        $db = Database::usandoEnv();
        try {
            $stmt = $db->query(
                "SELECT Id, Nome, Login, Email, Senha, TrocarSenha
                 FROM Usuario
                 WHERE Login = :login AND IdTipo = 7 AND Ativo = 1
                 LIMIT 1",
                ['login' => trim($login)]
            );
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin || !password_verify($senha, $admin['Senha'])) {
                return false;
            }
            unset($admin['Senha']);
            return $admin;
        } catch (PDOException $e) {
            error_log('[REBOBINE][AdminRepository::autenticar] ' . $e->getMessage());
            return false;
        }
    }

    public static function listar(): array
    {
        $db = Database::usandoEnv();
        $stmt = $db->query(
            "SELECT Id, Nome, Login, Email, DataInscricao
             FROM Usuario WHERE IdTipo = 7 AND Ativo = 1 ORDER BY Nome ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function criar(string $nome, string $login, string $email, string $senha): bool
    {
        $db = Database::usandoEnv();
        try {
            $stmt = $db->queryComTransacao(
                "INSERT INTO Usuario (Nome, Login, Senha, Email, IdTipo, TrocarSenha, Ativo)
                 VALUES (:nome, :login, :senha, :email, 7, 1, 1)",
                [
                    'nome' => $nome,
                    'login' => $login,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'email' => $email
                ]
            );
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            error_log('[REBOBINE][AdminRepository::criar] ' . $e->getMessage());
            return false;
        }
    }

    public static function atualizar(int $id, string $nome, string $login, string $email): bool
    {
        $resultado = self::atualizarStatus($id, $nome, $login, $email);
        return $resultado === 'ok' || $resultado === 'unchanged';
    }

    public static function atualizarStatus(int $id, string $nome, string $login, string $email): string
    {
        $db = Database::usandoEnv();
        try {
            $existente = $db->query(
                "SELECT Nome, Login, Email
                 FROM Usuario
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1
                 LIMIT 1",
                ['id' => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$existente) {
                return 'missing';
            }

            if (
                (string)$existente['Nome'] === $nome
                && (string)$existente['Login'] === $login
                && (string)$existente['Email'] === $email
            ) {
                return 'unchanged';
            }

            $stmt = $db->queryComTransacao(
                "UPDATE Usuario SET Nome = :nome, Login = :login, Email = :email
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1",
                ['nome' => $nome, 'login' => $login, 'email' => $email, 'id' => $id]
            );

            if ($stmt->rowCount() === 1) {
                return 'ok';
            }

            $confirmado = $db->query(
                "SELECT Nome, Login, Email
                 FROM Usuario
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1
                 LIMIT 1",
                ['id' => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (
                $confirmado
                && (string)$confirmado['Nome'] === $nome
                && (string)$confirmado['Login'] === $login
                && (string)$confirmado['Email'] === $email
            ) {
                return 'ok';
            }

            return 'missing';
        } catch (PDOException $e) {
            error_log('[REBOBINE][AdminRepository::atualizar] ' . $e->getMessage());
            return 'error';
        }
    }

    public static function alterarSenha(int $id, string $senha, bool $trocarNoProximoLogin = false): bool
    {
        $resultado = self::alterarSenhaStatus($id, $senha, $trocarNoProximoLogin);
        return $resultado === 'ok' || $resultado === 'unchanged';
    }

    public static function alterarSenhaStatus(int $id, string $senha, bool $trocarNoProximoLogin = false): string
    {
        $db = Database::usandoEnv();
        try {
            $existente = $db->query(
                "SELECT Senha, TrocarSenha
                 FROM Usuario
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1
                 LIMIT 1",
                ['id' => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$existente) {
                return 'missing';
            }

            if (
                password_verify($senha, (string)$existente['Senha'])
                && (int)$existente['TrocarSenha'] === ($trocarNoProximoLogin ? 1 : 0)
            ) {
                return 'unchanged';
            }

            $stmt = $db->queryComTransacao(
                "UPDATE Usuario SET Senha = :senha, TrocarSenha = :trocar
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1",
                [
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                    'trocar' => $trocarNoProximoLogin ? 1 : 0,
                    'id' => $id
                ]
            );

            if ($stmt->rowCount() === 1) {
                return 'ok';
            }

            $confirmado = $db->query(
                "SELECT Senha, TrocarSenha
                 FROM Usuario
                 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1
                 LIMIT 1",
                ['id' => $id]
            )->fetch(PDO::FETCH_ASSOC);

            if (
                $confirmado
                && password_verify($senha, (string)$confirmado['Senha'])
                && (int)$confirmado['TrocarSenha'] === ($trocarNoProximoLogin ? 1 : 0)
            ) {
                return 'ok';
            }

            return 'missing';
        } catch (PDOException $e) {
            error_log('[REBOBINE][AdminRepository::alterarSenha] ' . $e->getMessage());
            return 'error';
        }
    }

    public static function inativar(int $id, int $idAtual): string
    {
        if ($id === $idAtual) return 'self';
        $db = Database::usandoEnv();
        try {
            $stmt = $db->query("SELECT COUNT(*) AS total FROM Usuario WHERE IdTipo = 7 AND Ativo = 1");
            $total = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
            if ($total <= 1) return 'last';
            $update = $db->queryComTransacao(
                "UPDATE Usuario SET Ativo = 0 WHERE Id = :id AND IdTipo = 7 AND Ativo = 1",
                ['id' => $id]
            );
            return $update->rowCount() === 1 ? 'ok' : 'missing';
        } catch (PDOException $e) {
            error_log('[REBOBINE][AdminRepository::inativar] ' . $e->getMessage());
            return 'error';
        }
    }
}
