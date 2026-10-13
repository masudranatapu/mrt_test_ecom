<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Store a file on the public disk, deleting the old one if given.
     * Returns the stored path (relative to the public disk).
     */
    public function upload(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $this->delete($oldPath);

        return $file->store($directory, 'public');
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
