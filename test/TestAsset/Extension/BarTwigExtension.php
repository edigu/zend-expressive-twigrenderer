<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-twigrenderer for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-twigrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Twig\TestAsset\Extension;

use Twig\Extension\AbstractExtension;

class BarTwigExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'bar-twig-extension';
    }
}
