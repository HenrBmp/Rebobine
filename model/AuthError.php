<?php

namespace model;

use Exception;

class AuthError extends Exception {
    public function __construct() {
        parent::__construct('Id de usuário nao encontrado em $_SESSION. Usuário não está logado ou o valor não foi atribuído.');
    }
}
