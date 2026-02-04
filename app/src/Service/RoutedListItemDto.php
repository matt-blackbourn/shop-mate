<?php

namespace App\Service;

use App\Entity\ListItem;
use App\Entity\ProductPlacement;

final class RoutedListItemDto
{
    public function __construct(
        public ListItem $item,
        public ?ProductPlacement $placement,
        public ?int $targetNodeId,
        public int $distanceFromPrevious,
        public array $path
    ) {}
}

