<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

class CommandLayerDependencyTest extends TestCase
{
    #[Test]
    /**
     * Executa a rotina principal do metodo commands_depend_on_input_ports_instead_of_action_implementations.
     */
    public function commands_depend_on_input_ports_instead_of_action_implementations(): void
    {
        foreach ($this->commandFiles() as $fileInfo) {
            if ($this->isExcludedCommandFile($fileInfo)) {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            $this->assertIsString($contents);
            $this->assertStringContainsString(
                'use App\\Application\\Ports\\In\\',
                $contents,
                sprintf('Command [%s] must depend on at least one input port.', $fileInfo->getPathname())
            );
            $this->assertStringNotContainsString(
                'use App\\Actions\\',
                $contents,
                sprintf(
                    'Command [%s] must not depend on Action implementations directly.',
                    $fileInfo->getPathname()
                )
            );
        }
    }

    /**
     * @return array<int, SplFileInfo>
     */
    private function commandFiles(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(app_path('Console/Commands'))
        );

        $files = [];

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $files[] = $fileInfo;
        }

        return $files;
    }

    private function isExcludedCommandFile(SplFileInfo $fileInfo): bool
    {
        return $fileInfo->getFilename() === 'InspireCommand.php';
    }
}
