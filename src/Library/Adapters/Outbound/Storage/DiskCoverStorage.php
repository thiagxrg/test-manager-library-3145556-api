<?php

namespace Library\Adapters\Outbound\Storage;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Library\Application\Ports\Outbound\CoverStorage;

/** Covers live on local disk in development; the database only ever sees the opaque key (D-08). */
class DiskCoverStorage implements CoverStorage
{
    private string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?? base_path(env('COVERS_DIR', 'storage/app/covers'));

        if (! File::isDirectory($this->directory)) {
            File::makeDirectory($this->directory, 0755, true);
        }
    }

    public function store(string $contents, string $extension): string
    {
        $key = Str::uuid()->toString().'.'.$extension;

        File::put($this->path($key), $contents);

        return $key;
    }

    public function read(string $key): ?string
    {
        $path = $this->path($key);

        return File::exists($path) ? File::get($path) : null;
    }

    public function delete(string $key): void
    {
        $path = $this->path($key);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function path(string $key): string
    {
        return $this->directory.DIRECTORY_SEPARATOR.basename($key);
    }
}
