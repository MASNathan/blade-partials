<?php

namespace MASNathan\BladePartials;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use MASNathan\BladePartials\View\Factory;

class BladePartialsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton(Factory::class);

        View::composer('*', function ($view) use ($app) {
            $view->with('bladePartialsFactory', $app[Factory::class]);
        });

        Blade::directive('partial', function ($expression) {
            return sprintf("<?php \$bladePartialsFactory->compilePartial(
                %s, get_defined_vars(), function(\$file, \$vars) use (\$__env) { 
                    \$vars = array_except(\$vars, array('__data', '__path')); 
                    extract(\$vars); ?>", $expression);
        });

        Blade::directive('endpartial', function () {
            return "<?php echo \$__env->make(\$file, \$vars)->render(); }); ?>";
        });

        Blade::directive('block', function ($expression) {
            return "<?php \$bladePartialsFactory->compileBlock($expression); ?>";
        });

        Blade::directive('endblock', function () {
            return "<?php \$bladePartialsFactory->compileEndBlock(); ?>";
        });

        Blade::directive('optional', function ($expression) {
            $args = explode(',', $expression);
            if (count($args) == 1 || strtolower(trim($args[1])) == 'null') {
                return "<?php if (\$outputBlockCode = \$bladePartialsFactory->compileOptional($expression)):
                    echo \$outputBlockCode; else: ?>";
            }

            return "<?php echo \$bladePartialsFactory->compileOptional($expression) ?>";
        });

        Blade::directive('endoptional', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('required', function ($expression) {
            return "<?php echo \$bladePartialsFactory->compileRequired($expression); ?>";
        });
    }
}
