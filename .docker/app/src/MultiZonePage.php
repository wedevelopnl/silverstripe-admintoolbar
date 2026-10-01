<?php

declare(strict_types=1);

namespace App;

use Override;
use Page;
use SilverStripe\Forms\FieldList;
use WeDevelop\Grid\Forms\GridEditorField;

/**
 * Test-harness page type with two grid zones (main + sidebar) on the Main tab.
 *
 * Used by E2E tests to verify cross-zone drag isolation, backend rejection of
 * cross-zone section reorder attempts, and the zone step of the duplicate flow.
 *
 * Lives in the harness app rather than the module: it is a consumer-side page
 * type, so shipping it would give every install a page type and a table it
 * never asked for, plus a hard dependency on the project-level `Page` class
 * (which silverstripe/cms does not provide — it comes from the recipe).
 *
 * Not marked TestOnly: ClassManifest drops TestOnly classes from the manifest
 * and TableBuilder skips their tables outside test mode, so the page type would
 * not exist on the dev site the E2E fixtures drive over HTTP.
 */
class MultiZonePage extends Page
{
    private static string $table_name = 'App_MultiZonePage';

    private static string $singular_name = 'Multi-Zone Page';

    private static string $class_description = 'Test-harness page with main + sidebar grid zones';

    /** Prevent this page type from appearing in the CMS "Add new page" dropdown. */
    private static string $hide_ancestor = self::class;

    #[Override]
    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName('Content');

            $fields->addFieldToTab(
                'Root.Main',
                GridEditorField::create('GridEditorMain', (int) $this->ID, 'main'),
            );
            $fields->addFieldToTab(
                'Root.Main',
                GridEditorField::create('GridEditorSidebar', (int) $this->ID, 'sidebar'),
            );
        });

        // GridPageExtension::updateCMSFields() adds a single-zone "GridEditor"
        // during the extension chain; strip it after the chain so this page
        // exposes only the zone-specific editors above.
        $this->afterUpdateCMSFields(function (FieldList $fields): void {
            $fields->removeByName('GridEditor');
        });

        return parent::getCMSFields();
    }
}
