<?php

namespace MASNathan\BladePartials;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use MASNathan\BladePartials\View\Factory;
use MASNathan\BladePartials\View\Partial;

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
            $partialClass = Partial::class;
            $args = explode(',', $expression);

            $code = "<?php \$bladePartialsFactory->compilePartial(new $partialClass($expression), array_except(get_defined_vars(), array('__data', '__path')), function(\$vars) use (\$__env) { extract(\$vars); ?>";

            if (count($args) > 1) {
                $code .= "<?php echo \$__env->make(\$partial->getFile(), array_except(get_defined_vars(), array('__data', '__path')))->render(); }); ?>";
            }

            return $code;
        });

        Blade::directive('endpartial', function () {
            return "<?php echo \$__env->make(\$partial->getFile(), array_except(get_defined_vars(), array('__data', '__path')))->render(); }); ?>";
        });

        Blade::directive('block', function ($expression) {
            return "<?php \$partial->addBlock($expression); ?>";
        });

        Blade::directive('endblock', function () {
            return "<?php \$partial->endBlock(); ?>";
        });

        Blade::directive('optional', function ($expression) {
            $args = explode(',', $expression);
            if (count($args) == 1 || strtolower(trim($args[1])) == 'null') {
                return "<?php if (\$outputBlockCode = \$partial->optional($expression)):
                    echo \$outputBlockCode; else: ?>";
            }

            return "<?php echo \$partial->optional($expression) ?>";
        });

        Blade::directive('endoptional', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('condition', function ($expression) {
            $args = explode(',', $expression);
            if (count($args) == 1 || strtolower(trim($args[1])) == 'null') {
                return "<?php if (\$partial->condition($expression)): ?>";
            }

            return "<?php echo \$partial->conditionWithDefault($expression); ?>";
        });

        Blade::directive('endcondition', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('required', function ($expression) {
            return "<?php echo \$partial->required($expression); ?>";
        });
    }
}
