<?php

namespace model;

/**
 * Define possibilidades para a ordenacao dos dados fornecidos pelo banco de dados.
 */
enum Ordenacoes: string {
    case ASC = "ASC";
    case DESC = "DESC";
}
