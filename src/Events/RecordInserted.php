<?php

namespace MrFelipeMartins\Helix\Events;

use MrFelipeMartins\Helix\Models\Index;

class RecordInserted
{
    public function __construct(
        public Index $store,
        public string $id,
        /** @var array<int,float> */
        public array $vector,
        public mixed $metadata = null,
    ) {}
}
