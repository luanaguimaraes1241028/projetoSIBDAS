<?php

function validar_designacao(string $val): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = 'A designação é obrigatória.';
    return $erros;
}

function validar_categoria(string $val): array {
    $erros = [];
    if (empty($val) || !ctype_digit($val)) $erros[] = 'Selecione uma categoria válida.';
    return $erros;
}

function validar_marca(string $val): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = 'A marca é obrigatória.';
    return $erros;
}

function validar_modelo(string $val): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = 'O modelo é obrigatório.';
    return $erros;
}

function validar_numero_serie(string $val): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = 'O número de série é obrigatório.';
    return $erros;
}

function validar_fabricante(string $val): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = 'O fabricante é obrigatório.';
    return $erros;
}

function validar_ano(string $val): array {
    $erros = [];
    if (empty($val) || !preg_match('/^\d{4}$/', $val) || (int)$val < 1900 || (int)$val > (int)date('Y'))
        $erros[] = 'O ano de fabrico deve ser um valor entre 1900 e ' . date('Y') . '.';
    return $erros;
}

function validar_data(string $val, string $mensagem): array {
    $erros = [];
    if (empty(trim($val))) $erros[] = $mensagem;
    return $erros;
}

function validar_custo(string $val): array {
    $erros = [];
    if (empty($val) || !is_numeric($val) || (float)$val < 0)
        $erros[] = 'O custo de aquisição deve ser um valor numérico positivo.';
    return $erros;
}
