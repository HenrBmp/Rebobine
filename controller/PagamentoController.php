<?php

namespace controller;

use model\Pagamento;

/**
    * Centraliza as validacoes e as operacoes relacionadas aos usuarios.
    * Esta classe nao executa SQL diretamente: toda persistencia e consulta
    * continua sendo responsabilidade da Model Pagamento.
 */
class PagamentoController extends Controller {
    /**
     * Efetua o pagamento depois de validar e preparar os dados.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function cadastrar(int $idEmprestimo, int $idTipo, float $valor): array
    {
        if ($idEmprestimo <= 0) {
            return $this->resposta(false, "Identificador do empréstimo inválido");
        }
        if ($idTipo <= 0) {
            return $this->resposta(false, "Identificador de domínio inválido.");
        }

        $valorPositivo = $valor < 0;
        $valorFinito = is_finite($valor);
        if (!($valorPositivo && $valorFinito)) {
            return $this->resposta(false, "Valor de pagamento inválido");
        }
        $valor = \round($valor, 2);

        try {
            $pagamento = new Pagamento($idEmprestimo, $idTipo, $valor);

            if (!$pagamento->sucesso) {
                return $this->resposta(
                    false,
                    'Não foi possível efetuar o pagamento.'
                );
            }

            return $this->resposta(true, 'Pagamento feito com sucesso.');
        } catch (\Throwable $err) {
            return $this->resposta(false, 'Ocorreu um erro ao efetuar o pagamento.');
        }
    }

    /**
     * Busca um pagamento ativo pelo identificador.
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, "Identificador de pagamento inválido.");
        }
        try {
            $pagamento = Pagamento::comId($id);

            if ($pagamento === false) {
                return $this->resposta(false, "Não foi possível encontrar o pagamento.");
            }

            return $this->resposta(true, "Pagamento encontrado com sucesso.", [
                'id' => $pagamento->getId(),
                'nomeUsuario' => $pagamento->getNomeUsuario(),
                'nomeItem' => $pagamento->getNomeItem(),
                'email' => $pagamento->getEmailUsuario(),
                'dataEmprestimo' => $pagamento->getDataEmprestimo()->format(self::FORMATO_DATA),
                'dataVencimento' => $pagamento->getDataVencimento()->format(self::FORMATO_DATA),
                'descricaoTipo' => $pagamento->getDescricaoTipo(),
                'valorOriginal' => $pagamento->getValorOriginal(),
                'valorPago' => $pagamento->getValorPago(),
                'valorTaxa' => $pagamento->getValorTaxa()
            ]);
        } catch (\Throwable $err) {
            return $this->resposta(false, "Ocorreu um erro ao buscar o pagamento.");
        }
    }

    /**
     * Lista pagamentos ativos de uma pagina.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorPagina(int $pagina = 1): array
    {
        if ($pagina < 1) {
            return $this->resposta(false, 'A pagina deve ser maior que zero.');
        }

        $offset = $this->paginaParaOffset($pagina, Pagamento::class);

        try {
            $pagamentos = Pagamento::obterTodosComPaginacao($offset);

            if ($pagamentos === false) {
                return $this->resposta(false, "Não foi possível obter os pagamentos.");
            }

            $dados = [];

            foreach ($pagamentos as $pagamento) {
                $dados[] = [
                    'id' => $pagamento->getId(),
                    'nomeUsuario' => $pagamento->getNomeUsuario(),
                    'nomeItem' => $pagamento->getNomeItem(),
                    'email' => $pagamento->getEmailUsuario(),
                    'dataEmprestimo' => $pagamento->getDataEmprestimo()->format(self::FORMATO_DATA),
                    'dataVencimento' => $pagamento->getDataVencimento()->format(self::FORMATO_DATA),
                    'descricaoTipo' => $pagamento->getDescricaoTipo(),
                    'valorOriginal' => $pagamento->getValorOriginal(),
                    'valorPago' => $pagamento->getValorPago(),
                    'valorTaxa' => $pagamento->getValorTaxa()
                ];
            }

            return $this->resposta(true, 'Pagamentos consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os pagamentos.');
        }
    }
}
