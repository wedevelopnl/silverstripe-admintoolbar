<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\Page;

enum PublishState: string
{
    case Published = 'published';
    case Modified = 'modified';
    case Draft = 'draft';
    case Archived = 'archived';

    public static function of(bool $isPublished, bool $isModifiedOnDraft, bool $isArchived): self
    {
        return match (true) {
            $isArchived => self::Archived,
            $isPublished && $isModifiedOnDraft => self::Modified,
            $isPublished => self::Published,
            default => self::Draft,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Published => _t(self::class . '.PUBLISHED', 'Published'),
            self::Modified => _t(self::class . '.MODIFIED', 'Modified'),
            self::Draft => _t(self::class . '.DRAFT', 'Draft'),
            self::Archived => _t(self::class . '.ARCHIVED', 'Archived'),
        };
    }

    /** Literal class strings, so the Tailwind scanner finds them. */
    public function getBadgeClasses(): string
    {
        return match ($this) {
            self::Published => 'ssat:bg-green-200 ssat:text-green-800',
            self::Modified => 'ssat:bg-orange-200 ssat:text-orange-800',
            self::Draft => 'ssat:bg-blue-200 ssat:text-blue-800',
            self::Archived => 'ssat:bg-yellow-200 ssat:text-yellow-800',
        };
    }
}
