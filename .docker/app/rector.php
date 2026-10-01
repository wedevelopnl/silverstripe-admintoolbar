<?php

declare(strict_types=1);

use Netwerkstatt\SilverstripeRector\Set\SilverstripeLevelSetList;
use Netwerkstatt\SilverstripeRector\Set\SilverstripeSetList;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        '/module/src',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
    )
    ->withPhpSets(php83: true)
    ->withSets([
        SilverstripeSetList::CODE_STYLE,
        SilverstripeLevelSetList::UP_TO_SS_6_0,
    ])
    ->withSkip([
        // Subjective style rewrites that churn diffs without improving safety.
        FlipTypeControlToUseExclusiveTypeRector::class,
        PostIncDecToPreIncDecRector::class,
    ]);
