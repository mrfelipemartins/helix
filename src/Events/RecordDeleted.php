<?php

namespace MrFelipeMartins\Helix\Events;

use MrFelipeMartins\Helix\Models\Index;

class RecordDeleted
{
    public function __construct(
        public Index $store,
        public string $id,
    ) {}
}
