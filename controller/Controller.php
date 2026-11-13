<?php

namespace controller;


/**
 * Define métodos e propriedades comuns para todos os Controllers.
 */
class Controller {
    public const string FORMATO_DATA = "Y-m-d H:i:s";

    /*
     * Padroniza o formato das respostas da Controller.
     */
    final protected function resposta(bool $sucesso, string $mensagem, mixed $dados = null): array
    {
        $resposta = [
            'sucesso' => $sucesso,
            'mensagem' => $mensagem
        ];

        if ($dados !== null) {
            $resposta['dados'] = $dados;
        }

        return $resposta;
    }

    /**
     * Converte pagina para um offset de acordo com a constante ITENS_POR_SELECT da tabela fornecida.
     */
    final protected function paginaParaOffset(int $pagina, string $tabela): int {
        return ($pagina - 1) * $tabela::ITENS_POR_SELECT;
    }
}
