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

    public function testLabelsAndColours(): void
    {
        $this->assertSame(['Published', 'green'], [PublishState::Published->getLabel(), PublishState::Published->getColor()]);
        $this->assertSame(['Modified', 'orange'], [PublishState::Modified->getLabel(), PublishState::Modified->getColor()]);
        $this->assertSame(['Draft', 'blue'], [PublishState::Draft->getLabel(), PublishState::Draft->getColor()]);
        $this->assertSame(['Archived', 'yellow'], [PublishState::Archived->getLabel(), PublishState::Archived->getColor()]);
    }
}
