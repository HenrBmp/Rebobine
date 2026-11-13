<?php

namespace controller;

use model\Devolucao;

/**
    * Centraliza as validacoes e as operacoes relacionadas as devolucoes.
    * Esta classe nao executa SQL diretamente: toda persistencia e consulta
    * continua sendo responsabilidade da Model Devolucao.
 */
class DevolucaoController extends Controller
{
    /**
     * Cadastra uma devolucao para um emprestimo.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function cadastrar(int $idEmprestimo): array
    {
        if ($idEmprestimo <= 0) {
            return $this->resposta(false, 'Identificador de emprestimo invalido.');
        }

        try {
            $devolucao = new Devolucao($idEmprestimo);

            if (!$devolucao->sucesso) {
                return $this->resposta(false, 'Nao foi possivel cadastrar a devolucao.');
            }

            return $this->resposta(true, 'Devolucao cadastrada com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao cadastrar a devolucao.');
        }
    }

    /**
     * Busca uma devolucao ativa pelo identificador.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de devolucao invalido.');
        }

        try {
            $devolucao = Devolucao::comId($id);

            if ($devolucao === false) {
                return $this->resposta(false, 'Devolucao nao encontrada.');
            }

            return $this->resposta(true, 'Devolucao encontrada.', [
                'id' => $devolucao->getId(),
                'nomeUsuario' => $devolucao->getNomeUsuario(),
                'emailUsuario' => $devolucao->getEmailUsuario(),
                'nomeItem' => $devolucao->getNomeItem(),
                'dataEmprestimo' => $devolucao->getDataEmprestimo()->format(self::FORMATO_DATA),
                'dataDevolucao' => $devolucao->getDataDevolucao()->format(self::FORMATO_DATA)
            ]);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao consultar a devolucao.');
        }
    }

    /**
     * Lista devolucoes ativas do usuario logado de uma pagina.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorPagina(int $pagina = 1): array
    {
        if ($pagina < 1) {
            return $this->resposta(false, 'A pagina informada e invalida.');
        }

        $offset = $this->paginaParaOffset($pagina, Devolucao::class);

        try {
            $devolucoes = Devolucao::obterTodosComPaginacao($offset);

            if ($devolucoes === false) {
                return $this->resposta(false, 'Nao foi possivel consultar as devolucoes.');
            }

            $dados = [];

            foreach ($devolucoes as $devolucao) {
                $dados[] = [
                    'id' => $devolucao->getId(),
                    'nomeUsuario' => $devolucao->getNomeUsuario(),
                    'emailUsuario' => $devolucao->getEmailUsuario(),
                    'nomeItem' => $devolucao->getNomeItem(),
                    'dataEmprestimo' => $devolucao->getDataEmprestimo()->format(self::FORMATO_DATA),
                    'dataDevolucao' => $devolucao->getDataDevolucao()->format(self::FORMATO_DATA)
                ];
            }

            return $this->resposta(true, 'Devolucoes consultadas com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar as devolucoes.');
        }
    }
}
