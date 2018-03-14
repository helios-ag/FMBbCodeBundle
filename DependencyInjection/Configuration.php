<?php

namespace FM\BbcodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2013 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('fm_bbcode', 'array');

        $rootNode
            ->children()
                ->arrayNode('config')
                ->canBeUnset()
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('filters')
                            ->canBeUnset()
                            ->ignoreExtraKeys()
                            ->defaultValue(array())
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('classname')->end()
                                    ->scalarNode('class')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('hooks')
                            ->canBeUnset()
                            ->ignoreExtraKeys()
                            ->defaultValue(array())
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('classname')->end()
                                    ->scalarNode('class')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('messages')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('templates')
                            ->canBeUnset()
                            ->ignoreExtraKeys()
                            ->defaultValue(array())
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('path')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('filter_sets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('locale')->defaultValue('default')->end()
                            ->booleanNode('xhtml')->defaultTrue()->end()
                            ->booleanNode('strict')->defaultTrue()->end()
                            ->booleanNode('escaping')->defaultTrue()->end()
                            ->booleanNode('line_breaks')->defaultTrue()->end()
                            ->scalarNode('max_newlines')->defaultValue(3)->end()
                            ->arrayNode('filters')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode('hooks')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode('whitelist')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                           ->end()
                        ->end()
                    ->end()
                ->scalarNode('public_path')->defaultValue('%kernel.project_dir%/public')->end()
            ->end()
        ->end();

        $this->addEmoticonSection($rootNode);

        return $treeBuilder;
    }

    private function addEmoticonSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('emoticon')
                    ->info('emoticon configuration')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('resource')->end()
                        ->scalarNode('type')->end()
                        ->scalarNode('path')
                            ->defaultValue('/emoticons/')
                            ->validate()
                                ->ifTrue(function ($v) { return 0 !== strpos($v, '/'); })
                                ->then(function ($v) {
                                    $message = sprintf(
                                        'The "fm_bbcode.emoticon.path" '.
                                        'configuration must be start with a '.
                                        '"/", "%s" given.',
                                        $v
                                    );

                                    throw new \RuntimeException($message);
                                })
                            ->end()
                        ->end()
                        ->scalarNode('folder')
                            ->defaultValue('%kernel.project_dir%/vendor/mjohnson/decoda/emoticons')
                            ->end()
                        ->scalarNode('extension')->defaultValue('png')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
