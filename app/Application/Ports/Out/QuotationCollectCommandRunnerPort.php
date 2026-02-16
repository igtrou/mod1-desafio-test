<?php

namespace App\Application\Ports\Out;

interface QuotationCollectCommandRunnerPort
{
    /**
     * @param  array<string, mixed>  $arguments
     * @return array{exit_code: int, output: string}
     */
    public function run(array $arguments): array;
}
