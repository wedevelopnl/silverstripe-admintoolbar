<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Integration\Grid;

use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\List\SS_List;
use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Model\Menu;
use WeDevelop\Grid\Contract\ContainerInterface;
use WeDevelop\Grid\Contract\GridAdapterInterface;
use WeDevelop\Grid\Extensions\GridPageExtension;
use WeDevelop\Grid\Model\Column;
use WeDevelop\Grid\Model\GridElement;
use WeDevelop\Grid\Model\Row;
use WeDevelop\Grid\Model\Section;
use WeDevelop\Grid\Model\SharedBlockReference;

/**
 * The current page's grid as a read-only map with edit links. Enabled by
 * `_config/grid.yml` only when silverstripe-grid is installed; this is the
 * only class that references the grid module.
 */
class GridMenu extends Menu
{
    private static bool $enabled = false;

    private static int $order = 30;

    private static string $title = 'Grid';

    private static string $icon = 'font-icon-block-layout';

    /** @var array<string, string> */
    private static array $dependencies = [
        'gridAdapter' => '%$' . GridAdapterInterface::class,
    ];

    public GridAdapterInterface $gridAdapter;

    /** @var ArrayList<ArrayData>|null */
    private ?ArrayList $zones = null;

    /** Shown only when the dialog would list a node the member can view. */
    public function isSupported(): bool
    {
        return $this->getZones()->exists();
    }

    /**
     * Zones in display order: `main` first, the rest alphabetically. A zone
     * without a root the member can view is left out, as are zone-less roots
     * (invalid in grid, wedevelopnl/silverstripe-grid#490). Built once per
     * menu, which lives for one toolbar render.
     *
     * @return ArrayList<ArrayData>
     */
    public function getZones(): ArrayList
    {
        return $this->zones ??= $this->buildZones();
    }

    /**
     * @return ArrayList<ArrayData>
     */
    private function buildZones(): ArrayList
    {
        $page = $this->getContext()->page;
        $member = $this->getContext()->member;

        if ($page === null || !$page->hasExtension(GridPageExtension::class) || !$page->UseGrid) {
            return ArrayList::create();
        }

        // Not columnUnique(): the default Sort order joins the DISTINCT and repeats zones.
        /** @var list<string> $zones */
        $zones = $page->GridRoots()->column('Zone');
        $names = array_unique(array_filter($zones));
        usort($names, static fn (string $a, string $b): int => [$a !== 'main', $a] <=> [$b !== 'main', $b]);

        $visible = [];

        foreach ($names as $name) {
            /** @var ArrayList<GridElement> $roots */
            $roots = $page->GridZone($name);
            $nodes = $this->nodes($roots, $member);

            if ($nodes->exists()) {
                $visible[] = ['Name' => $name, 'Nodes' => $nodes];
            }
        }

        return ArrayList::create(array_map(
            static fn (array $zone): ArrayData => ArrayData::create($zone + ['ShowHeading' => count($visible) > 1]),
            $visible,
        ));
    }

    /**
     * @param SS_List<GridElement> $elements
     * @return ArrayList<ArrayData>
     */
    private function nodes(SS_List $elements, Member $member): ArrayList
    {
        $nodes = ArrayList::create();

        foreach ($elements as $element) {
            if ($element->canView($member)) {
                $nodes->push($this->node($element, $member));
            }
        }

        return $nodes;
    }

    private function node(GridElement $element, Member $member): ArrayData
    {
        $columns = $this->gridAdapter->getColumnCount();

        $kind = match (true) {
            $element instanceof SharedBlockReference => ['Kind' => 'shared', 'Link' => $this->blockLink($element, $member)],
            $element instanceof Section => ['Kind' => 'section'],
            $element instanceof Row => ['Kind' => 'row', 'Of' => $columns],
            $element instanceof Column => ['Kind' => 'column', 'Span' => $element->getGridSettings()->default->width, 'Of' => $columns],
            default => ['Kind' => 'element'],
        };

        return ArrayData::create($kind + [
            'Title' => $element->Title ?: $element->getType(),
            'Icon' => $element->config()->get('icon'),
            'Link' => $element->canEdit($member) ? $element->getCMSEditLink() : null,
            'Children' => $element instanceof ContainerInterface
                ? $this->nodes($this->children($element), $member)
                : ArrayList::create(),
        ]);
    }

    /**
     * Containers hold only grid elements; the relation is typed as its DataObject base.
     *
     * @param ContainerInterface<GridElement> $container
     * @return SS_List<GridElement>
     */
    private function children(ContainerInterface $container): SS_List
    {
        /** @var SS_List<GridElement> $children */
        $children = $container->getChildren();

        return $children;
    }

    /** A placement is edited in the shared-block library, not on the page. */
    private function blockLink(SharedBlockReference $reference, Member $member): ?string
    {
        $block = $reference->Block();

        return $block !== null && $block->canEdit($member) ? $block->getCMSEditLink() : null;
    }
}
