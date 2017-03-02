<?php

/**
 * A demo provider
 * @package Ace
 * @author renshan <1005110700@qq.com>
 */

namespace Ace\Provider;

use Pimple\Container;
use Plume\Provider\ProviderInterface;

class DemoProvider implements ProviderInterface
{
    private $container;

    /**
     * @param $plume
     */
    public function register(Container $plume)
    {
        $plume['provider.demo'] = $this;
        
        $this->container = $plume;
    }

    /**
     * @param $name
     */
    public function sayHello($name)
    {
        echo "Hello {$name}";
    }
}

?>
