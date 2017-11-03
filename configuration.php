<?php

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

interface Option {
    function name(): string;
    function build(): NodeDefinition;
}

class PulsatingErrorMessagesOption implements Option {
    function name(): string {
        return 'pulsating_error_messages';
    }
    function build(): NodeDefinition {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->name());
        $rootNode
            ->children()
                ->integerNode('size')
            ->end()
        ;
        return $rootNode;
    }
}

class MoreRowsOption implements Option {
    function name(): string {
        return 'more_rows';
    }
    function build(): NodeDefinition {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->name());
        $rootNode
            ->scalarPrototype()->defaultFalse()->end()
        ;
        return $rootNode;
    }
}

class MyConfiguration implements ConfigurationInterface {
	function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('test');

		$rootNode->
			children()
				->arrayNode('meta')
                    ->children()
                        ->scalarNode('name')->end()
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
                ->append($this->getOptionNodes())
            ->end()
        ;

		return $treeBuilder;
	}

	function getOptionNodes() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('options');

        $node = $rootNode->children();

        /** @var Option $option */
        $optionNames = [];
        foreach ($this->getOptions() as $optionClass) {
            $option = new $optionClass;
            $optionName = $option->name();
            if (in_array($optionName, $optionNames)) {
                throw new InvalidConfigurationException("Option used more than once: $optionName");
            }
            $optionNames[] = $optionName;
            $node->append($option->build());
        }

        $node->end();

        return $rootNode;
    }

    function getOptions() {
	    return [
            PulsatingErrorMessagesOption::class,
            MoreRowsOption::class
        ];
    }
}
