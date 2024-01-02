<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

interface AdminToolbarButtonInterface
{
    public function getHTML(): string;
    public function getOrder(): int;
    public function getName(): string;
    public function getExtraClasses(): string;
    public function getDataTags(): string;
}
