<?php

namespace App\Providers;

use Carbon\Carbon;

class DateHelpers
{
    public static function formatDate_dmY($value) {
	    return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
    }

    public static function formatDate_ddmmYYYY($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y');
    }

    public static function formatFloatValue($value) {
        $value = preg_replace('/\,/', '.', preg_replace('/\./', '', $value));
        return number_format($value, 2, '.', '');
    }
    
    public static function formatRealFormat($value) {        
        return number_format($value, 2, ',', '');
    }
}
