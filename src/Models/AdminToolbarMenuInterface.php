<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

interface AdminToolbarMenuInterface
{
    public function getName(): string;
    public function getIcon(): string;
    public function getHTML(): ?string;
    public function getOrder(): int;
}
