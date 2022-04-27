<?php

namespace App\Console\Commands\Exports\Csvstructure;

class Facturation {
	public static $Covage = [
        'column' => [
            'accesftth_id',
            'reference_pm',
            'etat_mad_ligne',
            'NomDSP',
            'ServiceId',
            'RefCmdClient',
            'RefCmdIftr',
            'RefCmdCovage',
            'DateRaccordement',
            'DateArriveeRaccordement',
            'DateResiliation',
            'RefPTO',
            'TypeAdduction',
            'PRISE EXISTANTE (O ou N)'
        ]
	];

    public static $Orange = [
        'column' => [
            'accesftth_id',
            'reference_pm',
            'etat_mad_ligne',
            'NomDSP',
            'ServiceId',
            'RefCmdClient',
            'RefCmdIftr',
            'RefCmdOrange',
            'DateRaccordement',
            'DateArriveeRaccordement',
            'DateResiliation',
            'RefPTO',
            'TypeAdduction',
            'PRISE EXISTANTE (O ou N)'
        ]
	];
}
