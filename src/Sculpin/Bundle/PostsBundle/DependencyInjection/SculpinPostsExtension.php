<?php

/*
 * This file is a part of Sculpin.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sculpin\Bundle\PostsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Sculpin Posts Extension.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class SculpinPostsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('sculpin_posts.paths', $config['paths']);
        if (isset($config['permalink'])) {
            $container->setParameter('sculpin_posts.permalink', $config['permalink']);
        }
        if (isset($config['layout'])) {
            $container->setParameter('sculpin_posts.layout', $config['layout']);
        } else {
            $container->setParameter('sculpin_posts.layout', null);
        }

        if (null !== $config['publish_drafts']) {
            $container->setParameter('sculpin_posts.publish_drafts', $config['publish_drafts']);
        } else {
            $container->setParameter('sculpin_posts.publish_drafts', 'prod' !== $container->getParameter('kernel.environment'));
        }

        if (empty($config['content_type_overrides'])) {
          $container->setParameter('sculpin_posts.content_type_overrides', array());
        }
        else {
           $container->setParameter('sculpin_posts.content_type_overrides', $config['content_type_overrides']);
        }
    }
}
