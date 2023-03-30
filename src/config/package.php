<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Periodable\Database\Seeders\MarinarPeriodableInstallSeeder"',
		],
        'remove' => [
            'php artisan db:seed --class="\Marinar\Periodable\Database\Seeders\MarinarPeriodableRemoveSeeder"',
        ]
	];
