<?php

namespace controller;

use model\Dominio;

/**
 * Centraliza as validacoes e as operacoes relacionadas aos dominios.
 * Esta classe nao executa SQL diretamente: toda persistencia e consulta
 * continua sendo responsabilidade da Model Dominio.
 */
class DominioController extends Controller
{
    /**
     * Cadastra um dominio depois de validar e preparar os dados.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function cadastrar(string $descricao, int $tipo): array
    {
        $descricao = trim($descricao);

        if ($descricao === '') {
            return $this->resposta(false, 'Preencha a descricao do dominio.');
        }

        if (mb_strlen($descricao, 'UTF-8') > 255) {
            return $this->resposta(false, 'A descricao deve possuir no maximo 255 caracteres.');
        }

        try {
            $dominio = new Dominio($descricao, $tipo);

            if (!$dominio->sucesso) {
                return $this->resposta(false, 'Nao foi possivel cadastrar o dominio. Verifique se a descricao ja esta cadastrada.');
            }

            return $this->resposta(true, 'Dominio cadastrado com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao cadastrar o dominio.');
        }
    }

    /**
     * Busca um dominio ativo pelo identificador.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de dominio invalido.');
        }

        try {
            $dominio = Dominio::comId($id);

            if ($dominio === false) {
                return $this->resposta(false, 'Dominio nao encontrado.');
            }

            return $this->resposta(true, 'Dominio encontrado.', [
                'id' => $dominio->getId(),
                'descricao' => $dominio->getDescricao(),
                'tipo' => $dominio->getTipo()
            ]);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao consultar o dominio.');
        }
    }

    /**
     * Lista todos os dominios ativos.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listar(): array
    {
        try {
            $dominios = Dominio::obterTodos();

            if ($dominios === false) {
                return $this->resposta(false, 'Nao foi possivel consultar os dominios.');
            }

            $dados = [];

            foreach ($dominios as $dominio) {
                $dados[] = [
                    'id' => $dominio->getId(),
                    'descricao' => $dominio->getDescricao(),
                    'tipo' => $dominio->getTipo()
                ];
            }

            return $this->resposta(true, 'Dominios consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os dominios.');
        }
    }

    /**
     * Lista os dominios ativos de um tipo.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorTipo(int $tipo): array
    {
        try {
            $dominios = Dominio::comTipo($tipo);

            if ($dominios === false) {
                return $this->resposta(false, 'Nao foi possivel consultar os dominios.');
            }

            $dados = [];

            foreach ($dominios as $dominio) {
                $dados[] = [
                    'id' => $dominio->getId(),
                    'descricao' => $dominio->getDescricao(),
                    'tipo' => $dominio->getTipo()
                ];
            }

            return $this->resposta(true, 'Dominios consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os dominios.');
        }
    }
}
