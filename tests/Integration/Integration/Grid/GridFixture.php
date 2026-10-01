<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Integration\Integration\Grid;

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\Queries\SQLUpdate;
use WeDevelop\Grid\Model\ContentElement;
use WeDevelop\Grid\Model\Row;
use WeDevelop\Grid\Model\Section;

/**
 * Loads `grid.yml` as written: the grid would otherwise scaffold a child into
 * every container the fixture writes before its listed children.
 */
trait GridFixture
{
    public function onBeforeLoadFixtures(): void
    {
        Config::modify()->set(Section::class, 'auto_scaffold', false);
        Config::modify()->set(Row::class, 'auto_scaffold', false);
    }

    /**
     * The grid gives every element a default title on write, so an untitled
     * element only exists in the database.
     */
    protected function untitleAside(): void
    {
        SQLUpdate::create(
            '"WeDevelop_Grid_GridElement"',
            ['"Title"' => ''],
            ['"ID"' => $this->idFromFixture(ContentElement::class, 'aside')],
        )->execute();
    }
}
