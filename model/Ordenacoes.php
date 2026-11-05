<?php

namespace model;

/**
 * Define possibilidades para a ordenacao dos dados fornecidos pelo banco de dados.
 */
enum Ordenacoes: string {
    /**
     * Ordena em ordem alfabetica crescente com base no campo Nome.
     */
    case NOME = "Nome";
    /**
     * Ordena em ordem alfabetica decrescente com base no campo Nome.
     */
    case NOME_DESC = "Nome DESC";
    /**
     * Ordena com base no Id, que é acrescido sequencialmente.
     */
    case RECENTE_ID = "Id ASC";
}
