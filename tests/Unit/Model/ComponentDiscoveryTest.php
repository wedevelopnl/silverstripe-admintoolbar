<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Unit\Model;

use LogicException;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Injector\SilverStripeServiceConfigurationLocator;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use WeDevelop\AdminToolbar\Button\QueriesButton;
use WeDevelop\AdminToolbar\Model\Button;
use WeDevelop\AdminToolbar\Model\Component;
use WeDevelop\AdminToolbar\Tests\Fixture\AbstractFixtureButton;
use WeDevelop\AdminToolbar\Tests\Fixture\EarlyFixtureButton;
use WeDevelop\AdminToolbar\Tests\Fixture\EndFixtureMenu;
use WeDevelop\AdminToolbar\Tests\Fixture\FixtureButton;
use WeDevelop\AdminToolbar\Tests\Fixture\FixtureMenu;
use WeDevelop\AdminToolbar\Tests\Fixture\FixtureMenuItem;
use WeDevelop\AdminToolbar\Tests\Fixture\OtherFixtureMenuItem;
use WeDevelop\AdminToolbar\Tests\Fixture\ReplacementFixtureButton;
use WeDevelop\AdminToolbar\Tests\Fixture\SameOrderFixtureButton;
use WeDevelop\AdminToolbar\Tests\Fixture\SubFixtureMenu;
use WeDevelop\AdminToolbar\Tests\Fixture\UnsupportedFixtureButton;
use WeDevelop\AdminToolbar\Toggle\QueriesToggle;
use WeDevelop\AdminToolbar\ToolbarContext;

final class ComponentDiscoveryTest extends SapphireTest
{
    public function testDisabledComponentsAreNotDiscovered(): void
    {
        $this->assertSame([], $this->fixtureClasses(Component::discover(Button::class, $this->context())));
    }

    public function testEnabledComponentsAreDiscoveredInOrderThenClassName(): void
    {
        $this->enable(FixtureButton::class, EarlyFixtureButton::class, SameOrderFixtureButton::class);

        $this->assertSame(
            [EarlyFixtureButton::class, FixtureButton::class, SameOrderFixtureButton::class],
            $this->fixtureClasses(Component::discover(Button::class, $this->context())),
        );
    }

    public function testAbstractClassesAreSkippedEvenWhenEnabled(): void
    {
        $this->enable(AbstractFixtureButton::class);

        $this->assertSame([], $this->fixtureClasses(Component::discover(Button::class, $this->context())));
    }

    public function testUnsupportedComponentsAreFilteredOut(): void
    {
        $this->enable(UnsupportedFixtureButton::class);

        $this->assertSame([], $this->fixtureClasses(Component::discover(Button::class, $this->context())));
    }

    public function testDiscoveredComponentsCarryTheContext(): void
    {
        $this->enable(FixtureButton::class);
        $context = $this->context();

        $found = $this->fixtures(Component::discover(Button::class, $context));

        $this->assertCount(1, $found);
        $this->assertSame($context, $found[0]->getContext());
    }

    public function testGetContextBeforeSetContextThrows(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(FixtureButton::class . ' was used before the toolbar gave it a context.');

        FixtureButton::create()->getContext();
    }

    public function testComponentsAreSupportedByDefault(): void
    {
        $this->assertTrue(FixtureButton::create()->setContext($this->context())->isSupported());
    }

    public function testInjectorReplacementIsHonoured(): void
    {
        $this->enable(FixtureButton::class);
        Config::modify()->merge(Injector::class, FixtureButton::class, ['class' => ReplacementFixtureButton::class]);
        // The locator caches each service's spec, and nested test injectors share it.
        Injector::inst()->setConfigLocator(new SilverStripeServiceConfigurationLocator());

        $this->assertSame(
            [ReplacementFixtureButton::class],
            $this->fixtureClasses(Component::discover(Button::class, $this->context())),
        );
    }

    public function testEnabledInjectorReplacementIsDiscoveredOnce(): void
    {
        $this->enable(FixtureButton::class, ReplacementFixtureButton::class);
        Config::modify()->merge(Injector::class, FixtureButton::class, ['class' => ReplacementFixtureButton::class]);
        Injector::inst()->setConfigLocator(new SilverStripeServiceConfigurationLocator());

        $this->assertSame(
            [ReplacementFixtureButton::class],
            $this->fixtureClasses(Component::discover(Button::class, $this->context())),
        );
    }

    public function testTitleIsTranslatedFromConfigDefault(): void
    {
        $this->assertSame('Fixture', FixtureButton::create()->getTitle());
    }

    public function testIconAndOrderComeFromConfig(): void
    {
        $button = FixtureButton::create();

        $this->assertSame('font-icon-edit', $button->getIcon());
        $this->assertSame(5, $button->getOrder());
    }

    public function testHookAndHiddenFlagComeFromConfig(): void
    {
        $fixture = FixtureButton::create();
        $queries = QueriesButton::create();

        $this->assertSame('data-fixture-button', $fixture->getHook());
        $this->assertFalse($fixture->isHiddenUntilEnabled());
        $this->assertSame('data-queries-button', $queries->getHook());
        $this->assertTrue($queries->isHiddenUntilEnabled());
        $this->assertSame('data-queries-toggle', QueriesToggle::create()->getHook());
    }

    public function testRenderingRequiresTheComponentAssets(): void
    {
        Requirements::clear();

        FixtureButton::create()->setContext($this->context())->forTemplate();

        $this->assertContainsPathEndingWith('client/dist/js/toolbar.js', array_keys(Requirements::backend()->getJavascript()));
        $this->assertContainsPathEndingWith('client/dist/css/toolbar.css', array_keys(Requirements::backend()->getCSS()));
    }

    public function testButtonRendersHookTitleAndIcon(): void
    {
        $html = FixtureButton::create()->setContext($this->context())->forTemplate();

        $this->assertStringContainsString('data-fixture-button', $html);
        $this->assertStringContainsString('Fixture', $html);
        $this->assertStringContainsString('font-icon-edit', $html);
        $this->assertStringContainsString('data-button-label', $html);
        $this->assertStringNotContainsString('ssat:hidden', $html);

        Config::modify()->set(FixtureButton::class, 'hidden_until_enabled', true);

        $this->assertStringContainsString('ssat:hidden', FixtureButton::create()->setContext($this->context())->forTemplate());
    }

    public function testMenuPlacementDefaultsToStart(): void
    {
        $this->assertSame('start', FixtureMenu::create()->getPlacement());
        $this->assertSame('end', EndFixtureMenu::create()->getPlacement());
    }

    public function testMenuDialogIdIsTheShortClassName(): void
    {
        $this->assertSame('FixtureMenu', FixtureMenu::create()->getDialogId());
    }

    public function testMenuItemsAttachToTheirMenuOnly(): void
    {
        $this->enable(FixtureMenu::class, EndFixtureMenu::class, FixtureMenuItem::class, OtherFixtureMenuItem::class);
        $context = $this->context();

        $this->assertSame(
            [FixtureMenuItem::class],
            $this->fixtureClasses(FixtureMenu::create()->setContext($context)->getItems()->toArray()),
        );
        $this->assertSame(
            [OtherFixtureMenuItem::class],
            $this->fixtureClasses(EndFixtureMenu::create()->setContext($context)->getItems()->toArray()),
        );
    }

    public function testItemsAttachToSubclassesOfTheirMenu(): void
    {
        $this->enable(FixtureMenuItem::class);

        $this->assertSame(
            [FixtureMenuItem::class],
            $this->fixtureClasses(SubFixtureMenu::create()->setContext($this->context())->getItems()->toArray()),
        );
    }

    public function testMenuItemLinkDefaultsToNull(): void
    {
        $this->assertNull(FixtureMenuItem::create()->getLink());
    }

    private function context(): ToolbarContext
    {
        $member = Member::create();
        // The toolbar only renders for the logged-in member, and built-in menu items rely on that.
        Security::setCurrentUser($member);

        return new ToolbarContext(null, $member, new HTTPRequest('GET', '/'));
    }

    /**
     * @param class-string<Component> ...$classes
     */
    private function enable(string ...$classes): void
    {
        foreach ($classes as $class) {
            Config::modify()->set($class, 'enabled', true);
        }
    }

    /**
     * @param array<Component> $components
     * @return list<Component>
     */
    private function fixtures(array $components): array
    {
        return array_values(array_filter(
            $components,
            static fn (Component $component): bool => str_starts_with($component::class, 'WeDevelop\\AdminToolbar\\Tests\\Fixture\\'),
        ));
    }

    /**
     * @param array<Component> $components
     * @return list<string>
     */
    private function fixtureClasses(array $components): array
    {
        return array_map(static fn (Component $component): string => $component::class, $this->fixtures($components));
    }

    /**
     * @param array<int|string> $paths
     */
    private function assertContainsPathEndingWith(string $suffix, array $paths): void
    {
        $matching = array_filter($paths, static fn (int|string $path): bool => str_ends_with((string) $path, $suffix));

        $this->assertNotEmpty($matching, sprintf('No required path ends with %s: %s', $suffix, implode(', ', $paths)));
    }
}
