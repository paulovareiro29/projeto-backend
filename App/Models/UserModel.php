<?php

namespace App\Models;

final class UserModel {


    public static function getFields(): array{
        return [
            'username',
            'pass',
            'nome',
            'email',
            'morada'
        ];
    }

    public static function getRoles(): array{
        return [
            'admin',
            'atleta',
            'treinador'
        ];
    }

}