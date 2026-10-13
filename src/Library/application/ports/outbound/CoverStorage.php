<?php

namespace Library\Application\Ports\Outbound;

interface CoverStorage
{
    /** Stores the file content and returns an opaque key. */
    public function store(string $contents, string $extension): string;

    public function read(string $key): ?string;

    public function delete(string $key): void;
}
