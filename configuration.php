<?php

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

interface Option {
    function build($name, NodeBuilder $node);
}

class PulsatingErrorMessagesOption implements Option {
    function build($name, NodeBuilder $node) {
        $node->arrayNode($name)
            ->children()
                ->integerNode('size')->isRequired()->end()
            ->end()
        ->end();
    }
}

class MinutesBeforeAndAfterOption implements Option {
    function build($name, NodeBuilder $node) {
        $node->arrayNode($name)
            ->children()
                ->integerNode('before')->end()
                ->integerNode('after')->end()
            ->end()
        ->end();
    }
}

class BooleanOption implements Option {
    function build($name, NodeBuilder $node) {
        $node->booleanNode($name)
            ->treatNullLike(true)
            ->validate()
                ->ifEmpty()
                ->thenInvalid('Cannot be false')
            ->end()
        ->end();
    }
}

class MyConfiguration implements ConfigurationInterface {
    function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('test');
        $rootNode = $treeBuilder->getRootNode();

        $node = $rootNode->
            children()
                ->arrayNode('meta')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('version')->end()
                    ->end()
                ->end()
                ->arrayNode('flow')
                    ->performNoDeepMerging()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('options')
                    ->children();
        $this->appendOptions($node);
                    $node->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    function appendOptions(NodeBuilder $node) {
        /** @var Option $option */
        foreach ($this->getOptions() as $optionName => $optionClass) {
            $option = new $optionClass;
            $option->build($optionName, $node);
        }
    }

    /**
     * @return Option[]
     */
    function getOptions() {
        return [
            'pulsating_error_messages' => PulsatingErrorMessagesOption::class,
            'more_rows' => BooleanOption::class,
            'time_override' => MinutesBeforeAndAfterOption::class,
        ];
    }
}
