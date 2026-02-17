<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class HttpLayerDependencyTest extends TestCase
{
    #[Test]
    /**
     * Executa a rotina principal do metodo http_layer_does_not_depend_on_domain_directly.
     */
    public function http_layer_does_not_depend_on_domain_directly(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(app_path('Http'))
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            $this->assertIsString($contents);
            $this->assertStringNotContainsString(
                'use App\\Domain\\',
                $contents,
                sprintf('Http file [%s] must not depend directly on Domain.', $fileInfo->getPathname())
            );
            $this->assertStringNotContainsString(
                'App\\Domain\\',
                $contents,
                sprintf('Http file [%s] must not reference Domain types directly.', $fileInfo->getPathname())
            );
        }
    }
}
