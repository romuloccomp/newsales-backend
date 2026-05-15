<?php

namespace App\Models;

class Customer
{
    public function __construct(
        public int $codigo,
        public string $nome,
        public string $email,
        public string $telefone,
        public string $cidade,
        public string $uf,
        public string $bairro,
        public string $cnpj,
        public string $inscricaoEstadual,
        public string $transportadora,
        public string $vsearch,
    ) {}
}
