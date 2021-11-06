<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cloud_cron');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('cron_watcher')
                    ->children()
                        ->integerNode('max_time_limit')->end()
                        ->integerNode('max_memory_limit')->end()
                        ->integerNode('max_loop_limit')->end()
                    ->end()
                ->end() // cron_watcher
            ->end();

        return $treeBuilder;
    }
}
