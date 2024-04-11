<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

interface AdminToolbarMenuItemInterface
{
    public function getName(): string;

    public function isSubMenu(): bool;

    public function getSubMenu(): ?AdminToolbarMenu;

    public function getOrder(): int;
}
