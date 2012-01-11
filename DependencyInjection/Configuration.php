<?php

namespace FM\BbCodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2011 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('fm_bb_code')
            ->children()
                ->scalarNode('locale')->cannotBeEmpty()->defaultValue('en-US')->end()
                ->scalarNode('xhtml')->defaultValue(true)->end()
                ->arrayNode('filters')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')->cannotBeEmpty()->defaultValue('enabled')->end()
                        ->scalarNode('block')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('code')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('email')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('image')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('list')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('quote')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('image')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('list')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('quote')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('text')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('url')->cannotBeEmpty()->defaultValue('disabled')->end()
                        ->scalarNode('video')->cannotBeEmpty()->defaultValue('disabled')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder->buildTree();
    }
}