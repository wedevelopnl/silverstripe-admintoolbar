<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

interface AdminToolbarToggleInterface
{
    public function getHTML(): string;

    public function getOrder(): int;

    public function getName(): string;
}
