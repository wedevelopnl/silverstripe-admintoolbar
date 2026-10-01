<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Unit\Menu\Page;

use PHPUnit\Framework\Attributes\DataProvider;
use SilverStripe\Dev\SapphireTest;
use WeDevelop\AdminToolbar\Menu\Page\PublishState;

final class PublishStateTest extends SapphireTest
{
    /**
     * @return iterable<string, array{bool, bool, bool, PublishState}>
     */
    public static function stateCombinations(): iterable
    {
        yield 'archived wins over published and modified' => [true, true, true, PublishState::Archived];
        yield 'archived wins over published' => [true, false, true, PublishState::Archived];
        yield 'archived wins over modified' => [false, true, true, PublishState::Archived];
        yield 'archived' => [false, false, true, PublishState::Archived];
        yield 'published with draft changes' => [true, true, false, PublishState::Modified];
        yield 'published' => [true, false, false, PublishState::Published];
        yield 'draft with changes' => [false, true, false, PublishState::Draft];
        yield 'draft' => [false, false, false, PublishState::Draft];
    }

    #[DataProvider('stateCombinations')]
    public function testStateFollowsThePageFlags(bool $published, bool $modified, bool $archived, PublishState $expected): void
    {
        $this->assertSame($expected, PublishState::of($published, $modified, $archived));
    }

    /**
     * @return iterable<string, array{PublishState, string, string}>
     */
    public static function labelsAndColours(): iterable
    {
        yield 'published' => [PublishState::Published, 'Published', 'green'];
        yield 'modified' => [PublishState::Modified, 'Modified', 'orange'];
        yield 'draft' => [PublishState::Draft, 'Draft', 'blue'];
        yield 'archived' => [PublishState::Archived, 'Archived', 'yellow'];
    }

    #[DataProvider('labelsAndColours')]
    public function testLabelsAndColours(PublishState $state, string $label, string $colour): void
    {
        $this->assertSame($label, $state->getLabel());
        $this->assertStringContainsString(sprintf('ssat:bg-%s-200', $colour), $state->getBadgeClasses());
        $this->assertStringContainsString(sprintf('ssat:text-%s-800', $colour), $state->getBadgeClasses());
    }
}
