<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

use LogicException;
use ReflectionClass;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Model\ModelData;
use SilverStripe\View\Requirements;
use WeDevelop\AdminToolbar\ToolbarContext;

/**
 * A part of the toolbar. Every concrete subclass of a component base is offered
 * to the toolbar unless its `enabled` config is false, so a project adds a
 * component by declaring a class and removes one with YAML.
 */
abstract class Component extends ModelData
{
    private static int $order = 10;

    private static bool $enabled = true;

    /** English default; translated through `<FQCN>.TITLE`. */
    private static string $title = '';

    private static string $icon = '';

    /** @var list<string> module resource paths, e.g. `vendor/module:client/dist/x.js` */
    private static array $javascript = [];

    /** @var list<string> module resource paths */
    private static array $stylesheets = [];

    private ?ToolbarContext $context = null;

    /**
     * @template T of Component
     * @param class-string<T> $baseClass
     * @return list<T>
     */
    public static function discover(string $baseClass, ToolbarContext $context): array
    {
        $found = [];

        /** @var array<string, class-string<T>> $classes */
        $classes = ClassInfo::subclassesFor($baseClass);

        foreach ($classes as $class) {
            if ((new ReflectionClass($class))->isAbstract() || Config::inst()->get($class, 'enabled') !== true) {
                continue;
            }

            $component = $class::create()->setContext($context);

            if ($component->isSupported()) {
                $found[] = $component;
            }
        }

        usort(
            $found,
            static fn (Component $a, Component $b): int => [$a->getOrder(), $a::class] <=> [$b->getOrder(), $b::class],
        );

        return $found;
    }

    public function setContext(ToolbarContext $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): ToolbarContext
    {
        if (!$this->context instanceof ToolbarContext) {
            throw new LogicException(static::class . ' was used before the toolbar gave it a context.');
        }

        return $this->context;
    }

    public function isSupported(): bool
    {
        return true;
    }

    public function getTitle(): string
    {
        /** @var string $title */
        $title = static::config()->get('title');

        return _t(static::class . '.TITLE', $title);
    }

    public function getIcon(): string
    {
        /** @var string $icon */
        $icon = static::config()->get('icon');

        return $icon;
    }

    public function getOrder(): int
    {
        /** @var int $order */
        $order = static::config()->get('order');

        return $order;
    }

    public function forTemplate(): string
    {
        $this->requireAssets();

        return $this->renderWith($this->getViewerTemplates())->forTemplate();
    }

    private function requireAssets(): void
    {
        /** @var list<string> $scripts */
        $scripts = static::config()->get('javascript');
        /** @var list<string> $sheets */
        $sheets = static::config()->get('stylesheets');

        foreach ($scripts as $script) {
            Requirements::javascript($script);
        }

        foreach ($sheets as $sheet) {
            Requirements::css($sheet);
        }
    }
}
