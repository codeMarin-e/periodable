<?php
    namespace Marinar\Periodable;

    use Marinar\Periodable\Database\Seeders\MarinarPeriodableInstallSeeder;

    class MarinarPeriodable {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarPeriodableInstallSeeder::class;
        }
    }
