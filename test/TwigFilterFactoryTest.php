<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-twigrenderer for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-twigrenderer/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Twig;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Twig\TwigFilter;
use Zend\Expressive\Twig\Exception\InvalidConfigException;
use Zend\Expressive\Twig\TwigFilterFactory;

class TwigFilterFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testCanCreateForNativeFunctions(): void
    {
        $config = [
            'name' => 'rot13',
            'filter' => 'str_rot13',
        ];

        $filter = TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
        $this->assertSame('rot13', $filter->getName());
    }

    public function testCanCreateForStaticMethods(): void
    {
        $config = [
            'name' => 'rot13',
            'filter' => 'str_rot13',
        ];

        $filter = TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
        $this->assertSame('rot13', $filter->getName());
    }

    public function getInvalidConfig(): array
    {
        return [
            [
                ['bar'],
                'Both "name" and "filter" keys are mandatory when defining twig filters'
            ],
            [
                [ 'name' => 'bar' ],
                'Both "name" and "filter" keys are mandatory when defining twig filters'
            ],
            [
                [ 'name' => 'bar', 'options' => [] ],
                'Both "name" and "filter" keys are mandatory when defining twig filters'
            ],
            [
                [ 'name' => 'bar', 'filter' => true ],
                'Key "filter" of a twig filter config should be a callable, '
                 . 'native function or fully qualified class name'
            ],
            [
                [ 'name' => 'bar', 'filter' => 42 ],
                'Key "filter" of a twig filter config should be a callable, '
                . 'native function or fully qualified class name'
            ],
        ];
    }

    /**
     * @dataProvider getInvalidConfig
     * @param array $config
     * @param string $message
     */
    public function testThrowsForInvalidConfig(array $config, string $message): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($message);
        TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
    }

    public function testCanCreateForCallable(): void
    {
        $config = [
            'name' => 'barFilter',
            'filter' => function ($input) {
                return 'Hello '. $input;
            },
        ];

        $filter = TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
        $this->assertSame('barFilter', $filter->getName());
    }

    public function testCanCreateForNativeFunction(): void
    {
        $config = [
            'name' => 'hash',
            'filter' => 'sha1',
        ];

        $filter = TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
        $this->assertSame('hash', $filter->getName());
    }

    public function testCanCreateForService(): void
    {
        $config = [
            'name' => 'bazFilter',
            'filter' => '\App\Twig\Filter\BazFilter',
        ];
        $this->container->has($config['filter'])->willReturn(true);
        $filterStub = new TwigFilter(
            'bazFilter',
            function () {
            }
        );
        $this->container->get($config['filter'])->willReturn($filterStub);

        $filter = TwigFilterFactory::createFilterFromConfig($this->container->reveal(), $config);
        $this->assertSame($filterStub, $filter);
    }
}
