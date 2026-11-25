<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um e-mail válido.',
    'unique' => 'O valor de :attribute já está cadastrado.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'enum' => 'Escolha uma opção válida para :attribute.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'string' => 'O campo :attribute deve ser um texto.',
    'array' => 'O campo :attribute deve ser uma lista.',
    'list' => 'O campo :attribute deve ser uma lista sequencial.',
    'distinct' => 'Há valores repetidos em :attribute.',
    'date_format' => 'Use o formato :format em :attribute.',
    'between' => ['numeric' => 'O campo :attribute deve estar entre :min e :max.'],
    'max' => [
        'string' => 'O campo :attribute deve ter até :max caracteres.',
        'array' => 'Selecione até :max itens.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array' => 'Selecione pelo menos :min item.',
        'numeric' => 'O campo :attribute deve ser maior ou igual a :min.',
    ],
    'attributes' => [
        'title' => 'título', 'description' => 'descrição', 'contract' => 'contrato',
        'paused' => 'situação', 'name' => 'nome', 'email' => 'e-mail',
        'password' => 'senha', 'per_page' => 'itens por página', 'ids' => 'seleção',
        'created_at' => 'criação', 'updated_at' => 'atualização',
    ],
];
