<?php

namespace App\BO;

class ProyectoBO
{
    public static function prepararDatos(array $data): array
    {
        return [
            'cliente_id'    => $data['cliente_id'],
            'nombre'        => $data['nombre'],
            'descripcion'   => $data['descripcion'] ,
            'status'        => $data['status'] ?? 'ACTIVO',
        ];
    }

    public static function datosParaInsert(array $data): array
    {
        $prepared = self::prepararDatos($data);

        $prepared['nombre'] = isset($prepared['nombre']) ? trim($prepared['nombre']) : '';
        $prepared['descripcion'] = isset($prepared['descripcion']) ? trim($prepared['descripcion']) : null;

        return $prepared;
    }

    public static function datosParaUpdate(array $data): array
    {
        $prepared = self::prepararDatos($data);

        return [
            'cliente_id'  => $prepared['cliente_id'],
            'nombre'      => isset($prepared['nombre']) ? trim($prepared['nombre']) : '',
            'descripcion' => $prepared['descripcion'] ?? null,
        ];
    }

    public static function prepararAsignaciones(array $usuariosIds): array
    {
        $usuarios = [];

        foreach ($usuariosIds as $uid) {
            $usuarios[] = [
                'usuario_id' => (int) $uid,
                'status' => 'ACTIVO',
            ];
        }
        return $usuarios;
    }

    public static function generarFolio(int $proyectoId): string
    {
        return 'LogProyecto-' . strtoupper(substr(sha1(time() . $proyectoId), 0, 6));
    }
    
}
