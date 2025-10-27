<?php

namespace App\Consts;

class StatusConsts
{
    const ACTIVO = 'ACTIVO';
    const ELIMINADO = 'ELIMINADO';
    public static $usuarioStatus = [
        self::ACTIVO,
        self::ELIMINADO
    ];

    public static $perfilStatus = [
        self::ACTIVO,
        self::ELIMINADO
    ];

}