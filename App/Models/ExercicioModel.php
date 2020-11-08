<?php

namespace App\Models;

final class ExercicioModel {

    public static function getFields(): array{
        return [
            'nome',
            'descricao',
            'tipoExercicio_id'
        ];
    }

    public static function getFieldsTipoExercicio(): array{
        return [
            'nome',
            'descricao'
        ];
    }

}