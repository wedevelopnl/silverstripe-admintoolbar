<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Tests\Unit;

use ReflectionProperty;
use RuntimeException;
use SilverStripe\Core\Manifest\Module;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Dev\SapphireTest;
use Symfony\Component\Yaml\Yaml;
use WeDevelop\AdminToolbar\Integration\Grid\GridMenu;

/**
 * `Only:` blocks cannot be switched at runtime, so the module's YAML is read as data.
 */
final class ConfigDefaultsTest extends SapphireTest
{
    public function testLiveEnvironmentDisablesTheQueriesButtonAndToggle(): void
    {
        $live = $this->documentWhere(
            'config.yml',
            static fn (array $header): bool => ($header['Only'] ?? null) === ['environment' => 'live'],
        );

        $this->assertSame(['enabled' => false], $live['WeDevelop\AdminToolbar\Button\QueriesButton'] ?? null);
        $this->assertSame(['enabled' => false], $live['WeDevelop\AdminToolbar\Toggle\QueriesToggle'] ?? null);
    }

    public function testContentControllerAndMemberExtensionsAreRegistered(): void
    {
        $base = $this->documentWhere('config.yml', static fn (array $header): bool => ($header['Name'] ?? null) === 'admintoolbar');

        $this->assertSame(
            ['extensions' => ['WeDevelop\AdminToolbar\Extension\ContentControllerExtension']],
            $base['SilverStripe\CMS\Controllers\ContentController'] ?? null,
        );
        $this->assertSame(
            ['extensions' => ['WeDevelop\AdminToolbar\Extension\MemberExtension']],
            $base['SilverStripe\Security\Member'] ?? null,
        );
    }

    public function testGridMenuIsEnabledOnlyWhenTheGridModuleExists(): void
    {
        $grid = $this->documentWhere(
            'grid.yml',
            static fn (array $header): bool => ($header['Only'] ?? null) === ['moduleexists' => 'wedevelopnl/silverstripe-grid'],
        );

        $this->assertSame([GridMenu::class => ['enabled' => true]], $grid);
        $this->assertFalse((new ReflectionProperty(GridMenu::class, 'enabled'))->getDefaultValue());
    }

    /**
     * @param callable(array<mixed>): bool $matchesHeader
     * @return array<mixed>
     */
    private function documentWhere(string $file, callable $matchesHeader): array
    {
        $module = ModuleLoader::getModule('wedevelopnl/silverstripe-admintoolbar');
        $this->assertInstanceOf(Module::class, $module);

        $contents = file_get_contents($module->getPath() . '/_config/' . $file);
        $this->assertIsString($contents);

        // ['', header1, body1, header2, body2, …]
        $parts = preg_split('/^---$/m', $contents);
        $this->assertIsArray($parts);

        for ($i = 1; $i + 1 < count($parts); $i += 2) {
            $header = Yaml::parse($parts[$i]);
            $body = Yaml::parse($parts[$i + 1]);

            if (is_array($header) && $matchesHeader($header)) {
                $this->assertIsArray($body);

                return $body;
            }
        }

        throw new RuntimeException('No matching document in _config/' . $file);
    }
}
