<?php

namespace App\Providers;

use Carbon\Carbon;

class DateHelpers
{

    /**
     * cONVERTE DATAHORA PARA O FORMATO 2024-10-10
     * @param mixed $value
     * @return string
     */
    public static function formatDate_dmY($value) {
        return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
    }

    /**
     * CONVERTE DATAHORA PARA O FORMATO 10/10/2024
     * @param mixed $value
     * @return string
     */
    public static function formatDate_ddmmYYYY($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y');
    }

    /**
     * CONVERTE DATAHORA PARA O FORMATO 10/10/2024 10:30:00
     * @param mixed $value
     * @return string
     */
    public static function formatDate_ddmmYYYYHHIISS($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y H:i:s');
    }

    /**
     * Converte de 1.200,00 para 1200.00 para salvas no Bancod e dados
     * @param mixed $value
     * @return string
     */
    public static function formatFloatValue($value) {
        if (empty($value)) {
            return '0.00';
        }
        $value = preg_replace('/\,/', '.', preg_replace('/\./', '', $value));
        return number_format((float)$value, 2, '.', '');
    }

    /**
     * Converte de 1200.00 para 1200,00 para mostrar no formato de Reais na tela
     * @param mixed $value
     * @return string
     */
    public static function formatRealFormat($value) {
        if (empty($value)) {
            return '0,00';
        }
        return number_format((float)$value, 2, ',', '');
    }
}
