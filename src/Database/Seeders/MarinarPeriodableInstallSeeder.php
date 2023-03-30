<?php
    namespace Marinar\Periodable\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Periodable\MarinarPeriodable;

    class MarinarPeriodableInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_periodable';
            static::$packageDir = MarinarPeriodable::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
