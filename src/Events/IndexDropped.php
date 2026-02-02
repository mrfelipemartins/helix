<?php

namespace MrFelipeMartins\Helix\Events;

use MrFelipeMartins\Helix\Models\Index;

class IndexDropped
{
    public function __construct(
        public Index $store,
    ) {}
}
