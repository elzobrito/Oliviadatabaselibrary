<?php

namespace OliviaDatabasePublico;

require dirname(__DIR__) . '/vendor/autoload.php';

$c = new Configuracoes();
/**
 * $c->update(['status'], ['id = ?'], [12, 6]);
 * Este método update recebe três parâmetros. O primeiro parâmetro é uma matriz que contém os nomes das colunas que serão atualizadas. No caso, apenas a coluna 'status' seria atualizada. O segundo parâmetro é uma string SQL que indica quais registros devem ser atualizados. Neste caso, os registros com o ID igual a 12 e 6 seriam atualizados. O terceiro parâmetro é uma matriz de valores que substituirão os placeholders (?) dentro da string SQL.
 */
// $c->update(['status'], ['id = ?'], [12, 6]);

/**
 * 
 * $c->delete(2);
 * Este método delete exclui um registro do banco de dados que corresponda ao ID fornecido. Neste caso, o registro com ID igual a 2 seria excluído.
 */

// $c->delete(2);

/**
 * Este método save insere um novo registro na tabela do banco de dados. O primeiro parâmetro é uma matriz que contém os nomes das colunas nas quais os valores serão inseridos. O segundo parâmetro é uma matriz de valores correspondentes que serão inseridos nas colunas especificadas.
 */
// $c->save(
//     [
//         'parametro',
//         'valor',
//         'status'
//     ],
//     ['chatgpt', 'site', 1]
// );
/*
 Este método all retorna todas as linhas de uma determinada tabela do banco de dados.
*/
print_r($c->all());
