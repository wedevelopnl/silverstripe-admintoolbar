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

    /** Tailwind colour family; the Phase 1 CSS safelists `ss-at-bg-<colour>-200` / `ss-at-text-<colour>-800`. */
    public function getColor(): string
    {
        return match ($this) {
            self::Published => 'green',
            self::Modified => 'orange',
            self::Draft => 'blue',
            self::Archived => 'yellow',
        };
    }
}
