<?php

namespace Stratadox\TableLoader;

use ArrayAccess;
use Stratadox\IdentityMap\MapsObjectsByIdentity;

interface ContainsResultingObjects extends ArrayAccess
{
    public function identityMap(): MapsObjectsByIdentity;
}
