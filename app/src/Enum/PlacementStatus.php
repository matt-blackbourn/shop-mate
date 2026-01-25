<?php

namespace App\Enum;

enum PlacementStatus: string
{
    case SYSTEM = 'system';        // globally confirmed
    case CONFIRMED = 'confirmed';  // confirmed *for the current user*
    case PROVISIONAL = 'provisional'; // placed by someone else, needs confirmation
    case CATEGORY = 'category';    // inferred
    case NONE = 'none';
}

// Correct precedence (from the current user’s perspective)

// Highest → lowest

// Current user has an active PRODUCT placement
// → CONFIRMED
// Even if a SYSTEM placement exists elsewhere

// SYSTEM placement exists
// → SYSTEM
// Other users’ disagreements are hidden

// Another user has an active PRODUCT placement
// → PROVISIONAL
// Bait to confirm

// CATEGORY placement exists
// → CATEGORY

// Nothing
// → NONE
