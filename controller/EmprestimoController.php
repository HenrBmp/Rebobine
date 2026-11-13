<?php

namespace controller;

use DateTime;
use model\Emprestimo;
use model\Item;

/**
    * Centraliza as validacoes e as operacoes relacionadas aos emprestimos.
    * Esta classe nao executa SQL diretamente: toda persistencia e consulta
    * continua sendo responsabilidade da Model Emprestimo.
 */
class EmprestimoController extends Controller
{
    /**
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array {
        if ($id <= 0) {
            return $this->resposta(false, "Identificador de empréstimo inválido.");
        }

        try {
            $emprestimo = Emprestimo::comId($id);

            if ($emprestimo === false) {
                return $this->resposta(false, 'Empréstimo não encontrado.');
            }

            return $this->resposta(true, "Empréstimo encontrado.", [
                "id" => $emprestimo->getId(),
                "nomeItem" => $emprestimo->getNomeItem(),
                "nomeUsuario" => $emprestimo->getNomeUsuario(),
                "emailUsuario"=> $emprestimo->getEmailUsuario(),
                "dataEmprestimo" => $emprestimo->getDataEmprestimo()->format(self::FORMATO_DATA),
                "dataVencimento" => $emprestimo->getDataVencimento()->format(self::FORMATO_DATA),
                "valorItem" => $emprestimo->getValorItem(),
                "atrasado" => $emprestimo->getAtrasado(),
                "empresaItem" => $emprestimo->getEmpresaItem()
            ]);
        } catch (\Throwable $err) {
            return $this->resposta(false, "Ocorreu um erro ao consultar o empréstimo.");
        }
    }

    /**
     * Lista emprestimos ativos do usuario logado de uma pagina.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorPagina(int $pagina = 1): array
    {
        if ($pagina < 1) {
            return $this->resposta(false, 'A pagina informada e invalida.');
        }

        $offset = $this->paginaParaOffset($pagina, Emprestimo::class);

        try {
            $emprestimos = Emprestimo::obterTodosComPaginacao($offset);

            if ($emprestimos === false) {
                return $this->resposta(false, 'Nao foi possivel consultar os emprestimos.');
            }

            $dados = [];

            foreach ($emprestimos as $emprestimo) {
                $dados[] = [
                    'id' => $emprestimo->getId(),
                    'nomeItem' => $emprestimo->getNomeItem(),
                    'nomeUsuario' => $emprestimo->getNomeUsuario(),
                    'emailUsuario' => $emprestimo->getEmailUsuario(),
                    'dataEmprestimo' => $emprestimo->getDataEmprestimo()->format(self::FORMATO_DATA),
                    'dataVencimento' => $emprestimo->getDataVencimento()->format(self::FORMATO_DATA),
                    'valorItem' => $emprestimo->getValorItem(),
                    'atrasado' => $emprestimo->getAtrasado(),
                    'empresaItem' => $emprestimo->getEmpresaItem()
                ];
            }

            return $this->resposta(true, 'Emprestimos consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os emprestimos.');
        }
    }

    /**
     * Cria um emprestimo depois de validar o item e a data de vencimento.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function criar(int $idItem, string $dataVencimento): array
    {
        if ($idItem <= 0) {
            return $this->resposta(false, 'Identificador de item invalido.');
        }

        if (str_contains($dataVencimento, "\0")) {
            return $this->resposta(false, 'Informe uma data de vencimento valida no formato Y-m-d H:i:s.');
        }

        $dataVencimentoOriginal = $dataVencimento;
        $dataVencimento = DateTime::createFromFormat('!' . self::FORMATO_DATA, $dataVencimento);
        $errosData = DateTime::getLastErrors();

        if (
            $dataVencimento === false
            || ($errosData !== false && ($errosData['warning_count'] > 0 || $errosData['error_count'] > 0))
            || $dataVencimento->format(self::FORMATO_DATA) !== $dataVencimentoOriginal
        ) {
            return $this->resposta(false, 'Informe uma data de vencimento valida no formato Y-m-d H:i:s.');
        }

        if ($dataVencimento <= new DateTime()) {
            return $this->resposta(false, 'A data de vencimento deve ser futura.');
        }

        try {
            if (Item::comId($idItem) === false) {
                return $this->resposta(false, 'Item nao encontrado.');
            }

            $emprestimo = new Emprestimo($idItem, $dataVencimento);

            if (!$emprestimo->sucesso) {
                return $this->resposta(false, 'Nao foi possivel criar o emprestimo.');
            }

            return $this->resposta(true, 'Emprestimo criado com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao criar o emprestimo.');
        }
    }

    /**
     * Realiza a exclusao logica de um emprestimo do usuario logado.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function inativar(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de emprestimo invalido.');
        }

        try {
            if (!Emprestimo::inativar($id)) {
                return $this->resposta(false, 'Nao foi possivel inativar o emprestimo.');
            }

            return $this->resposta(true, 'Emprestimo inativado com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao inativar o emprestimo.');
        }
    }

    /**
     * Retorna o valor para o pagamento do emprestimo especificado.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function obterValorDePagamento(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de emprestimo invalido.');
        }

        try {
            $valorPagamento = Emprestimo::obterValorDePagamento($id);

            if ($valorPagamento === false) {
                return $this->resposta(false, 'Não foi possivel obter o valor de pagamento.');
            }

            return $this->resposta(true, 'Valor base do pagamento consultado com sucesso.', [
                'valor' => $valorPagamento
            ]);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao consultar o valor do pagamento.');
        }
    }
}
