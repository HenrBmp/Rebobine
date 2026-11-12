<?php

namespace controller;


/**
 * Define métodos comuns para todos os Controllers.
 */
class Controller {
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
}
