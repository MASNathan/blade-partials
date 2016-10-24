<?php
namespace MASNathan\BladePartials\View;

use MASNathan\BladePartials\Exceptions\RequiredBlockNotFoundException;

class Partial
{
    /**
     * Storage for our blocks.
     *
     * @var array
     */
    protected $blocks = [];

    protected $file;

    public function __construct($file, array $defaultBlocks = [])
    {
        $this->file = $file;
        $this->blocks = $defaultBlocks;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Start injecting content into a block.
     *
     * @param  string $block
     * @param  string $content
     *
     * @return void
     */
    public function addBlock($block, $content = null)
    {
        if (is_null($content)) {
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
        return $this->hasBlock($block) ? $this->blocks[$block] : $default;
    }

    public function condition($block)
    {
        return $this->hasBlock($block) && $this->blocks[$block];
    }

    public function conditionWithDefault($block, $default = null)
    {
        return $this->condition($block) ? $default : null;
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

    public function hasBlock($block)
    {
        return isset($this->blocks[$block]);
    }
}

