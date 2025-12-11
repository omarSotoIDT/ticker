<?php

namespace App\BO;

class DashboardBO
{
    public static function armarCardsFromTotales(array $totales): array
    {
        $total      = isset($totales['total']) ? (int) $totales['total'] : 0;
        $cerrados   = isset($totales['cerrados']) ? (int) $totales['cerrados'] : 0;
        $cancelados = isset($totales['cancelados']) ? (int) $totales['cancelados'] : 0;
        $urgentes   = isset($totales['urgentes']) ? (int) $totales['urgentes'] : 0;

        $activos = isset($totales['activos']) ? (int) $totales['activos'] : 0;

        return [
            'total'      => $total,
            'activos'    => $activos,
            'cerrados'   => $cerrados,
            'cancelados' => $cancelados,
            'urgentes'   => $urgentes,
        ];
    }

    public static function armarDataset(array $rows, string $labelKey, string $valueKey): array
    {
        $labels = [];
        $data = [];
        foreach ($rows as $r) {
            $label = is_array($r) ? ($r[$labelKey] ?? '') : ($r->$labelKey ?? '');
            $value = is_array($r) ? ($r[$valueKey] ?? 0) : ($r->$valueKey ?? 0);
            $labels[] = self::normalizarEtiqueta($label);
            $data[] = (int) $value;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public static function normalizarEtiqueta($label): string
    {
        if ($label === null) return '';
        $s = trim((string) $label);
        $s = mb_strtolower($s);
        $s = ucwords($s);
        return $s;
    }
}