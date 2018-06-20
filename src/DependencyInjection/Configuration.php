<?php
declare(strict_types = 1);

namespace Norsys\GoogleTagManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    const CONFIG_ROOT_NODE = 'norsys_google_tag_manager';

    /**
     * Keys to split dynamic and static configuration
     */
    const CONFIG_STATIC_KEY  = 'static';
    const CONFIG_DYNAMIC_KEY = 'dynamic';

    /**
     * @var NodeBuilder
     */
    private $nodeBuilder;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->nodeBuilder = new NodeBuilder();
    }

    /**
     * Generates the configuration tree builder to google tags config
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::CONFIG_ROOT_NODE);

        $rootNode
            ->children()
                ->scalarNode('id')
                    ->info('Google Tag Manager Identicator (GTM ID)')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('data_layer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->addDefaultSection())
                        ->append($this->addPagesSection())
                        ->append($this->addAliasesSection())
                    ->end()
                ->end()
                ->append($this->addOnEventSection())->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * Add default section
     *
     * @return ArrayNodeDefinition
     */
    public function addDefaultSection()
    {
        return $this->nodeBuilder
            ->arrayNode('default')
                ->addDefaultsIfNotSet()
                ->isRequired()
                ->children()
                    ->arrayNode(self::CONFIG_STATIC_KEY)
                        ->prototype('scalar')->defaultValue('')->end()
                    ->end()
                    ->arrayNode(self::CONFIG_DYNAMIC_KEY)
                        ->prototype('scalar')->defaultValue('')->end()
                    ->end()
                ->end();
    }

    /**
     * Add pages section
     *
     * @return ArrayNodeDefinition
     */
    public function addPagesSection()
    {
        return $this->nodeBuilder
            ->arrayNode('pages')
                ->defaultValue([])
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode(self::CONFIG_STATIC_KEY)
                            ->defaultValue([])
                            ->prototype('scalar')->defaultValue('')->end()
                        ->end()
                        ->arrayNode(self::CONFIG_DYNAMIC_KEY)
                            ->defaultValue([])
                            ->prototype('scalar')->defaultValue('')->end()
                        ->end()
                    ->end()
                ->end();
    }

    /**
     * Add aliases section
     *
     * @return ArrayNodeDefinition
     */
    public function addAliasesSection()
    {
        return $this->nodeBuilder
            ->arrayNode('aliases')
                ->defaultValue([])
                ->useAttributeAsKey('name')
                ->prototype('scalar')
            ->end();
    }

    /**
     * Add on event section
     *
     * @return ArrayNodeDefinition
     */
    public function addOnEventSection()
    {
        return $this->nodeBuilder
            ->arrayNode('on_event')
                ->addDefaultsIfNotSet()
                ->beforeNormalization()
                    ->ifTrue(function ($values) {
                        if (isset($values['enabled']) === false || $values['enabled'] === false) {
                            return true;
                        }

                        return false;
                    })
                    ->then(function ($values) {
                        unset($values['name']);
                        unset($values['container']);

                        return $values;
                    })
                ->end()
                ->validate()
                    ->ifTrue(function ($values) {
                        if (isset($values['enabled']) === false || $values['enabled'] !== true) {
                            return false;
                        }

                        if (isset($values['name']) === false || strlen((string) $values['name']) === 0) {
                            return true;
                        }

                        if (isset($values['container']) === false || strlen((string) $values['container']) === 0) {
                            return true;
                        }

                        return false;
                    })
                    ->thenInvalid(
                        'With norsys_google_tag_manager.on_event enabled,
                        you must configure norsys_google_tag_manager.on_event.name
                        and norsys_google_tag_manager.on_event.container parameters'
                    )
                ->end()
                ->children()
                    ->booleanNode('enabled')
                        ->info('Enable call google tag manager on javascript event instead of page finish loaded')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('name')
                        ->info('Event name to call google tag manager')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('container')
                        ->info(
                            'HTML element on which the event is sent,
                            use jQuery syntax (samples: "body", "#my-id", ".my-class", etc...)'
                        )
                        ->defaultNull()
                    ->end()
                ->end();
    }
}
