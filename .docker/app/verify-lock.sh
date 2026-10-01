#!/bin/sh
# Fail when the committed lock no longer covers the module's own requirements.
#
# The harness depends on the module through a `path` repository, and composer
# does NOT re-check a path package's requires when installing from a lock. Add a
# `require` to the module's composer.json without regenerating the lock and
# `composer install` prints nothing, exits 0, and simply omits the package —
# which then surfaces as a class-not-found somewhere unrelated. Verified against
# composer 2.10.
#
# So ask composer directly: re-resolve ONLY the module and see whether that
# changes any other locked package. The module's own entry always changes (its
# reference tracks the git HEAD of /module), so it is excluded from the compare.
set -e

cp /app/composer.lock /tmp/composer.lock.committed
composer update wedevelopnl/silverstripe-admintoolbar \
    --no-install --no-scripts --no-interaction --quiet
cp /app/composer.lock /tmp/composer.lock.resolved
cp /tmp/composer.lock.committed /app/composer.lock

php <<'PHP'
<?php

/** @return array<string, string> */
$packages = static function (string $path): array {
    $lock = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $out = [];
    foreach ([...$lock['packages'] ?? [], ...$lock['packages-dev'] ?? []] as $package) {
        // The module's own reference tracks git HEAD, so it differs on every
        // commit and says nothing about whether the lock is complete.
        if ($package['name'] !== 'wedevelopnl/silverstripe-admintoolbar') {
            $out[$package['name']] = $package['version'];
        }
    }
    ksort($out);

    return $out;
};

$committed = $packages('/tmp/composer.lock.committed');
$resolved = $packages('/tmp/composer.lock.resolved');

$added = array_diff_key($resolved, $committed);
$removed = array_diff_key($committed, $resolved);
$changed = array_filter(
    array_intersect_key($resolved, $committed),
    static fn (string $version, string $name): bool => $committed[$name] !== $version,
    ARRAY_FILTER_USE_BOTH,
);

if ($added === [] && $removed === [] && $changed === []) {
    echo "Lock covers the module's requirements.\n";
    exit(0);
}

fwrite(STDERR, "The committed lock does not cover the module's current requirements.\n\n");
foreach ($added as $name => $version) {
    fwrite(STDERR, "  missing: {$name} {$version}\n");
}
foreach ($removed as $name => $version) {
    fwrite(STDERR, "  stale:   {$name} {$version}\n");
}
foreach ($changed as $name => $version) {
    fwrite(STDERR, "  wrong:   {$name} {$committed[$name]} -> {$version}\n");
}
fwrite(STDERR, "\nRun `task relock` and commit the result.\n");

exit(1);
PHP
