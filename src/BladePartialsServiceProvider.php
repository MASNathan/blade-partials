<?php

namespace MASNathan\BladePartials;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use MASNathan\BladePartials\View\Factory;

class BladePartialsServiceProvider extends ServiceProvider
{

    public function register()
    {
        $app = $this->app;

        $app->singleton(Factory::class);

        View::composer('*', function ($view) use ($app) {
            $view->with('bladePartialsFactory', $app[Factory::class]);
        });

        Blade::directive('partial', function ($expression) {
            return sprintf("<?php \$bladePartialsFactory->partial(
                %s, get_defined_vars(), function(\$file, \$vars) use (\$__env) { 
                    \$vars = array_except(\$vars, array('__data', '__path')); 
                    extract(\$vars); ?>", $expression);
        });

        Blade::directive('endpartial', function ($expression) {
            return "<?php echo \$__env->make(\$file, \$vars)->render(); }); ?>";
        });

        Blade::directive('block', function ($expression) {
            return "<?php \$bladePartialsFactory->block($expression); ?>";
        });

        Blade::directive('endblock', function ($expression) {
            return "<?php \$bladePartialsFactory->endBlock(); ?>";
        });

        Blade::directive('optional', function ($expression) {
            return "<?php echo \$bladePartialsFactory->optional($expression); ?>";
        });

        Blade::directive('required', function ($expression) {
            return "<?php echo \$bladePartialsFactory->required($expression); ?>";
        });
    }
}
