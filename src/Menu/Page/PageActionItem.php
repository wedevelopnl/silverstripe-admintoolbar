<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\Page;

use ReflectionClass;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Menu\PageMenu;
use WeDevelop\AdminToolbar\Model\MenuItem;

/**
 * A state change on the current page, offered in the Page menu and executed by
 * {@see \WeDevelop\AdminToolbar\Controller\PageActionController}. Both read the
 * same rules from here, so the menu never offers what the endpoint refuses.
 */
abstract class PageActionItem extends MenuItem
{
    private static string $menu = PageMenu::class;

    /** The `action` value the page-action endpoint receives. */
    private static string $action = '';

    /** English default; translated through `<FQCN>.SUCCESS`. */
    private static string $success_message = '';

    private static bool $destructive = false;

    public static function forAction(string $action): ?self
    {
        /** @var array<string, class-string<PageActionItem>> $classes */
        $classes = ClassInfo::subclassesFor(self::class, false);

        foreach ($classes as $class) {
            if (
                !(new ReflectionClass($class))->isAbstract()
                && Config::inst()->get($class, 'enabled') === true
                && Config::inst()->get($class, 'action') === $action
                && $action !== ''
            ) {
                return $class::create();
            }
        }

        return null;
    }

    /** Whether the page's current state admits this action. */
    abstract public function appliesTo(SiteTree $page): bool;

    abstract public function isAllowedFor(SiteTree $page, Member $member): bool;

    abstract public function perform(SiteTree $page): void;

    public function isSupported(): bool
    {
        $context = $this->getContext();

        return $context->page instanceof SiteTree
            && $this->appliesTo($context->page)
            && $this->isAllowedFor($context->page, $context->member);
    }

    public function getAction(): string
    {
        /** @var string $action */
        $action = static::config()->get('action');

        return $action;
    }

    public function getSuccessMessage(): string
    {
        /** @var string $message */
        $message = static::config()->get('success_message');

        return _t(static::class . '.SUCCESS', $message);
    }

    public function isDestructive(): bool
    {
        /** @var bool $destructive */
        $destructive = static::config()->get('destructive');

        return $destructive;
    }

    public function getPageID(): int
    {
        return (int) $this->getContext()->page?->ID;
    }
}
