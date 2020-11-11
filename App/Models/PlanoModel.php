<?php

namespace App\Models;

final class PlanoModel {

    public static function getFields(): array{
        return [
            'treinador_id',
            'nome',
            'descricao',
            'data_inicial',
            'data_final'
        ];
    }

    public static function getFieldsBlocoExercicio(): array {
        return [
            'exercicio_id',
            'series',
            'repeticoes',
            'carga',
            'tempo_distancia',
        ];
    }

}