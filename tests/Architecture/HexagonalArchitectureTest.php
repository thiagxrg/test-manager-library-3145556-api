<?php

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

class HexagonalArchitectureTest extends TestCase
{
    public function test_domain_and_application_do_not_import_laravel_or_eloquent(): void
    {
        $forbidden = ['Illuminate\\', 'Laravel\\', 'Eloquent\\', 'DB::', 'Schema::'];
        $dirs = [__DIR__ . '/../../src/Library/Domain', __DIR__ . '/../../src/Library/Application'];

        foreach ($dirs as $dir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }
                $contents = (string) file_get_contents($file->getPathname());
                foreach ($forbidden as $needle) {
                    $this->assertStringNotContainsString(
                        $needle,
                        $contents,
                        $file->getPathname() . ' must not reference ' . $needle
                    );
                }
            }
        }
    }
}