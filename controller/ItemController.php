<?php

namespace controller;

use model\Item;
use model\Dominio;

/**
    * Centraliza as validacoes e as operacoes relacionadas aos itens.
    * Esta classe nao executa SQL diretamente: toda persistencia e consulta
    * continua sendo responsabilidade da Model Item.
 */
class ItemController extends Controller
{
    /**
     * Cadastra um item depois de validar e preparar os dados.
     *
     * @return array{sucesso: bool, mensagem: string}
     */
    public function cadastrar(string $nome, string $empresa, float $valor, int $tipo): array
    {
        $nome = trim($nome);
        $empresa = trim($empresa);

        if ($nome === '' || $empresa === '') {
            return $this->resposta(false, 'Preencha todos os campos.');
        }

        if (mb_strlen($nome, 'UTF-8') > 255) {
            return $this->resposta(false, 'O nome deve possuir no maximo 255 caracteres.');
        }

        if (mb_strlen($empresa, 'UTF-8') > 255) {
            return $this->resposta(false, 'A empresa deve possuir no maximo 255 caracteres.');
        }

        if (!is_finite($valor) || $valor <= 0) {
            return $this->resposta(false, 'Informe um valor valido para o item.');
        }

        if ($tipo <= 0) {
            return $this->resposta(false, 'Identificador de dominio invalido.');
        }

        $valor = round($valor, 2);
        if ($valor <= 0) {
            return $this->resposta(false, 'Informe um valor valido para o item.');
        }

        try {
            if (Dominio::comId($tipo) === false) {
                return $this->resposta(false, 'Dominio nao encontrado.');
            }

            $item = new Item($nome, $empresa, $valor, $tipo);

            if (!$item->sucesso) {
                return $this->resposta(false, 'Nao foi possivel cadastrar o item.');
            }

            return $this->resposta(true, 'Item cadastrado com sucesso.');
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao cadastrar o item.');
        }
    }

    /**
     * Busca um item ativo pelo identificador.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function buscarPorId(int $id): array
    {
        if ($id <= 0) {
            return $this->resposta(false, 'Identificador de item invalido.');
        }

        try {
            $item = Item::comId($id);

            if ($item === false) {
                return $this->resposta(false, 'Item nao encontrado.');
            }

            return $this->resposta(true, 'Item encontrado.', [
                'id' => $item->getId(),
                'nome' => $item->getNome(),
                'empresa' => $item->getEmpresa(),
                'valor' => $item->getValor(),
                'descricaoTipo' => $item->getDescricao()
            ]);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao consultar o item.');
        }
    }

    /**
     * Lista itens ativos de uma pagina.
     *
     * @return array{sucesso: bool, mensagem: string, dados?: array}
     */
    public function listarPorPagina(int $pagina = 1): array
    {
        if ($pagina < 1) {
            return $this->resposta(false, 'A pagina informada e invalida.');
        }

        $offset = $this->paginaParaOffset($pagina, Item::class);

        try {
            $itens = Item::obterTodosComPaginacao($offset);

            if ($itens === false) {
                return $this->resposta(false, 'Nao foi possivel consultar os itens.');
            }

            $dados = [];

            foreach ($itens as $item) {
                $dados[] = [
                    'id' => $item->getId(),
                    'nome' => $item->getNome(),
                    'empresa' => $item->getEmpresa(),
                    'valor' => $item->getValor(),
                    'descricaoTipo' => $item->getDescricao()
                ];
            }

            return $this->resposta(true, 'Itens consultados com sucesso.', $dados);
        } catch (\Throwable $erro) {
            return $this->resposta(false, 'Ocorreu um erro ao listar os itens.');
        }
    }
}
