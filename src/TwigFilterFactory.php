<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-twigrenderer for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-twigrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Twig;

use Psr\Container\ContainerInterface;
use Twig\TwigFilter;

use function array_key_exists;
use function is_array;
use function is_callable;
use function is_string;

/**
 * Create and return a Twig filter instances
 */
class TwigFilterFactory
{
    public static function createFilterFromConfig(ContainerInterface $container, array $filterConfig): TwigFilter
    {
        if (! array_key_exists('name', $filterConfig) || ! array_key_exists('filter', $filterConfig)) {
            throw new Exception\InvalidConfigException(
                'Both "name" and "filter" keys are mandatory when defining twig filters'
            );
        }

        $filter = $filterConfig['filter'];

        if (is_string($filter) && $container->has($filter)) {
            return $container->get($filter);
        }

        if (! is_array($filter) && ! is_callable($filter)) {
            throw new Exception\InvalidConfigException(
                'Key "filter" of a twig filter config should be a callable, '
                . 'native function or fully qualified class name'
            );
        }

        return new TwigFilter($filterConfig['name'], $filter, $filterConfig['options'] ?? []);
    }
}
