<?php

namespace App\DTOs;

class CustomerDTO
{
    public function __construct(
        public readonly int $codigo,
        public readonly string $nome,
        public readonly string $email,
        public readonly string $telefone,
        public readonly string $cidade,
        public readonly string $uf,
        public readonly string $bairro,
        public readonly string $cnpj,
        public readonly string $inscricaoEstadual,
        public readonly string $transportadora,
        public readonly string $nr_tabpre,
        public readonly string $vsearch,
    ) {}

    public static function fromArray(array $data): self
    {
        dd($data);
        return new self(
            codigo: $data['codigo'],
            nome: $data['nome'] ?? "",
            email: $data['e-mail'] ?? '',
            telefone: $data['telefone_1'] ?? '',
            cidade: $data['cidade'] ?? "",
            uf: $data['uf'] ?? "",
            bairro: $data['bairro'] ?? "",
            cnpj: $data['cgc'] ?? "",
            inscricaoEstadual: $data['ins-estadual'] ?? "",
            transportadora: $data['transp-nome'] ?? "",
            nr_tabpre: $data['nr_tabpre'],
            vsearch: $data["vsearch"] ?? ""
        );
    }

    public function toArray(): array
    {
        return [
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'bairro' => $this->bairro,
            'cnpj' => $this->cnpj,
            'inscricaoEstadual' => $this->inscricaoEstadual,
            'transportadora' => $this->transportadora,
            'nr_tabpre' => $this->nr_tabpre,
            'vsearch' => $this->vsearch
        ];
    }
}
