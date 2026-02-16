<?php

namespace App\Application\Ports\Out;

interface EnvFileEditorPort
{
    /**
     * @param  array<string, bool|int|string|null>  $variables
     */
    public function update(array $variables): void;
}
