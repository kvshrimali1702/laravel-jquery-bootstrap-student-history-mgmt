<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload file to storage.
     */
    public function upload(UploadedFile $file, string $directory = 'uploads', ?string $disk = null): ?string
    {
        $disk = $disk ?? $this->getDefaultDisk();

        if ($this->isOracleBucketDisk($disk)) {
            return $this->uploadToOracleBucket($file, $directory);
        }

        return $this->uploadToLocal($file, $directory, $disk);
    }

    /**
     * Upload file to local storage.
     */
    private function uploadToLocal(UploadedFile $file, string $directory, string $disk): string
    {
        $path = $file->store($directory, $disk);

        return Storage::disk($disk)->url($path);
    }

    /**
     * Upload file to Oracle bucket with encrypted link.
     */
    private function uploadToOracleBucket(UploadedFile $file, string $directory): string
    {
        // TODO: Implement Oracle bucket upload with encrypted link
        // This is a placeholder for production implementation
        // You'll need to configure Oracle Cloud Storage credentials
        // and implement the encryption logic for the file links

        $path = $file->store($directory, 'oracle');

        // Generate encrypted link
        return $this->generateEncryptedLink($path);
    }

    /**
     * Generate encrypted link for Oracle bucket file.
     */
    private function generateEncryptedLink(string $path): string
    {
        // TODO: Implement encryption logic for Oracle bucket links
        // This should generate a secure, time-limited encrypted URL
        return Storage::disk('oracle')->temporaryUrl($path, now()->addHours(24));
    }

    /**
     * Get default disk based on environment.
     */
    private function getDefaultDisk(): string
    {
        if (config('app.env') === 'production') {
            return config('filesystems.default_oracle', 'oracle');
        }

        return config('filesystems.default', 'local');
    }

    /**
     * Check if disk is Oracle bucket.
     */
    private function isOracleBucketDisk(string $disk): bool
    {
        return $disk === 'oracle' || str_starts_with($disk, 'oracle');
    }
}
