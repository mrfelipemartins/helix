<?php

namespace MrFelipeMartins\Helix\Events;

use MrFelipeMartins\Helix\Models\Index;

class IndexCreated
{
    public function __construct(
        public Index $store,
    ) {}
}
