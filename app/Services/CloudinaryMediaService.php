<?php

namespace App\Services;

use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CloudinaryMediaService
{
    /**
     * @return array{url: string, public_id: string}
     */
    public function upload(UploadedFile $file, string $folder): array
    {
        if ($this->cloudinaryReady()) {
            return $this->uploadToCloudinary($file, $folder);
        }

        return $this->uploadToLocal($file, $folder);
    }

    public function delete(?string $publicId): void
    {
        if ($publicId === null || $publicId === '') {
            return;
        }

        if (str_starts_with($publicId, 'local:')) {
            Storage::disk('public')->delete(substr($publicId, 6));

            return;
        }

        if (! $this->cloudinaryReady()) {
            return;
        }

        try {
            $this->cloudinary()->uploadApi()->destroy($publicId, [
                'resource_type' => 'image',
            ]);
        } catch (ApiError) {
            // Ignore remote delete failures so local records can still be removed.
        }
    }

    /**
     * @return array{url: string, public_id: string}
     */
    private function uploadToCloudinary(UploadedFile $file, string $folder): array
    {
        try {
            $result = $this->cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'order-easy/'.$folder,
                'resource_type' => 'image',
                'overwrite' => false,
            ]);
        } catch (ApiError $exception) {
            throw new RuntimeException('Image upload failed: '.$exception->getMessage(), previous: $exception);
        }

        return [
            'url' => (string) ($result['secure_url'] ?? $result['url'] ?? ''),
            'public_id' => (string) ($result['public_id'] ?? ''),
        ];
    }

    /**
     * @return array{url: string, public_id: string}
     */
    private function uploadToLocal(UploadedFile $file, string $folder): array
    {
        $path = $file->store('order-easy/'.$folder, 'public');

        if ($path === false) {
            throw new RuntimeException('Image upload failed. Could not save the file locally.');
        }

        return [
            'url' => Storage::disk('public')->url($path),
            'public_id' => 'local:'.$path,
        ];
    }

    private function cloudinaryReady(): bool
    {
        return filled(config('filesystems.disks.cloudinary.cloud'))
            && filled(config('filesystems.disks.cloudinary.key'))
            && filled(config('filesystems.disks.cloudinary.secret'));
    }

    private function cloudinary(): Cloudinary
    {
        return app(Cloudinary::class);
    }
}
