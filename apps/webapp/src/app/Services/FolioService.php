<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FolioService
{
    public static function obtener($clave)
    {
        $folio = DB::table('folios_globales')->where('clave', $clave)->lockForUpdate()->value('folio');
        DB::table('folios_globales')->where('clave', $clave)->update(['folio' => $folio + 1]);
        return $folio;
    }
}