<?php

namespace controller;

use model\Usuario;

/**
    * Centraliza as validacoes e as operacoes relacionadas aos usuarios.
    * Esta classe nao executa SQL diretamente: toda persistencia e consulta
    * continua sendo responsabilidade da Model Usuario.
 */

class UsuarioController extends Controller
{
    /**
     * Cadastra um usuario depois de validar e preparar os dados.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function cadastrar(string $nome, string $email, string $senha): array
    {
        // Remove espacos excedentes dos campos textuais.
        $nome = trim($nome);
        $email = trim($email);

        // Valida os campos obrigatorios.
        if ($nome === '' || $email === '' || $senha === '') {
            return $this->resposta(false, 'Preencha todos os campos.');
        }

        // Mantem os valores dentro dos limites do esquema Usuario.
        if (mb_strlen($nome, 'UTF-8') > 255) {
            return $this->resposta(false, 'O nome deve possuir no maximo 255 caracteres.');
        }

        // Valida o formato do endereco de e-mail.
        if (\strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->resposta(false, 'Informe um e-mail valido.');
        }

        // Exige pelo menos 8 caracteres e evita o limite de 72 bytes do bcrypt.
        if (mb_strlen($senha, 'UTF-8') < 8) {
            return $this->resposta(false, 'A senha deve possuir pelo menos 8 caracteres.');
        }

        if (\strlen($senha) > 72) {
            return $this->resposta(false, 'A senha nao pode ultrapassar 72 bytes.');
        }

        // Gera um hash seguro. A senha original nao deve ser armazenada, por questões obvias de seguranca.
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        if ($senhaHash === false) {
            return $this->resposta(false, 'Nao foi possivel proteger a senha.');
        }

        try {
            // A Model realiza o INSERT ao ser instanciada.
            $usuario = new Usuario($nome, $senhaHash, $email);

            if (!$usuario->sucesso) {
                return $this->resposta(
                    false,
                    'Nao foi possivel cadastrar o usuario. Verifique se o e-mail ja esta cadastrado.'
                );
            }

            return $this->resposta(true, 'Usuario cadastrado com sucesso.');
        } catch (\Throwable $erro) {
            // Evita expor detalhes internos ou dados sensiveis ao usuario.
            return $this->resposta(false, 'Ocorreu um erro ao cadastrar o usuario.');
        }
    }

    /**
     * Busca um usuario ativo pelo identificador.
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de usuario invalido.');
        }

        try {
            $usuario = Usuario::comId($id);

            if ($usuario === false) {
                return $this->resposta(false, 'Usuario nao encontrado.');
            }

            return $this->resposta(true, 'Usuario encontrado.', [
                'id' => $usuario->getId(),
                'nome' => $usuario->getNome(),
                'email' => $usuario->getEmail(),
                'dataInscricao' => $usuario->getDataInscricao()->format('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao consultar o usuario.');
        }
    }

    /**
     * Lista usuarios ativos de uma pagina.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorPagina(int $pagina = 1): array
    {
        if ($pagina < 1) {
            return $this->resposta(false, 'A pagina deve ser maior que zero.');
        }

        // A Model recebe o OFFSET do SQL, e nao o numero da pagina.
        $offset = $this->paginaParaOffset($pagina, Usuario::class);

        try {
            $usuarios = Usuario::obterTodosComPaginacao($offset);

            if ($usuarios === false) {
                return $this->resposta(false, 'Nao foi possivel consultar os usuarios.');
            }

            // Converte os Contracts em arrays para facilitar o uso no frontend.
            $dados = [];

            foreach ($usuarios as $usuario) {
                $dados[] = [
                    'id' => $usuario->getId(),
                    'nome' => $usuario->getNome(),
                    'email' => $usuario->getEmail(),
                    'dataInscricao' => $usuario->getDataInscricao()->format('Y-m-d H:i:s')
                ];
            }

            return $this->resposta(true, 'Usuarios consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os usuarios.');
        }
    }

    /**
     * Realiza a exclusao logica de um usuario.
     * @return array{sucesso: bool, mensagem: string}
     */
    public function inativar(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de usuario invalido.');
        }

        try {
            if (!Usuario::inativar($id)) {
                return $this->resposta(false, 'Nao foi possivel inativar o usuario.');
            }

            return $this->resposta(true, 'Usuario inativado com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao inativar o usuario.');
        }
    }
}
