<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CloudCronExtension extends Extension
{
    /**
     * @param array<int, string> $configs
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['cron_watcher'])) {
            $container->setParameter('cron_watcher.max_memory_limit', $config['cron_watcher']['max_memory_limit']);
            $container->setParameter('cron_watcher.max_time_limit', $config['cron_watcher']['max_time_limit']);
            $container->setParameter('cron_watcher.max_loop_limit', $config['cron_watcher']['max_loop_limit']);
        }
    }

    public function getAlias(): string
    {
        return 'cloud_cron';
    }
}
