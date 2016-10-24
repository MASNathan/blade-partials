<?php
namespace MASNathan\BladePartials\View;

class Factory
{
    /**
     * Render a partial by echoing its contents.
     * The variables defined outside the scope of this block
     * (i.e. within our template) are passed in so we can use them.
     *
     * @param  string   $file
     * @param  array    $vars
     * @param  callable $callback
     *
     * @return void
     */
    public function compilePartial(Partial $partial, $vars, $callback)
    {
        $vars['partial'] = $partial;

        $callback($vars);
    }
}
