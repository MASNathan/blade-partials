<?php
namespace MASNathan\BladePartials\View;

use MASNathan\BladePartials\Exceptions\RequiredBlockNotFoundException;

class Factory
{
    /**
     * Storage for our blocks.
     *
     * @var array
     */
    protected $blocks = [];

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
    public function partial($file, $vars, $callback)
    {
        $callback($file, $vars);

        $this->flushBlocks();
    }

    /**
     * Start injecting content into a block.
     *
     * @param  string $block
     * @param  string $content
     *
     * @return void
     */
    public function block($block, $content = '')
    {
        if ($content === '') {
            ob_start() && $this->blocks[] = $block;
        } else {
            $this->blocks[$block] = $content;
        }
    }

    /**
     * Stop injecting content into a block.
     *
     * @return void
     */
    public function endBlock()
    {
        $last = array_pop($this->blocks);

        $this->blocks[$last] = ob_get_clean();
    }

    /**
     * Gets the value of a optional block
     *
     * @param string $block
     * @param string $default
     *
     * @return string|null
     */
    public function optional($block, $default = null)
    {
        return isset($this->blocks[$block]) ? $this->blocks[$block] : $default;
    }

    /**
     * Gets the value of a required block
     *
     * @param string $block
     *
     * @return string
     * @throws RequiredBlockNotFoundException
     */
    public function required($block)
    {
        if (!isset($this->blocks[$block])) {
            throw new RequiredBlockNotFoundException($block);
        }

        return $this->blocks[$block];
    }

    /**
     * Get the entire array of blocks.
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Flush all the block contents.
     *
     * @return void
     */
    public function flushBlocks()
    {
        $this->blocks = [];
    }
}

