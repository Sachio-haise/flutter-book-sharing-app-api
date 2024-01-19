<?php

namespace App\Http\Media;

use Exception;
use Illuminate\Support\Str;
use App\Models\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FilesystemException;
use Illuminate\Support\Facades\Storage as Storages;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Storage
{
    private static $directory = 'media/';

    public static function upload(UploadedFile $file, $model, $previewId = null, $customFolder = null)
    {
        $model = strtolower(class_basename($model));

        if ($customFolder) {
            $customFolder = self::folderNameGenerate($customFolder);
        }

        if ($previewId !== null) {
            return self::updateMedia($file, $previewId, $model, $customFolder);
        } else {
            return self::createMedia($file, $model, $customFolder);
        }
    }

    public static function delete($id)
    {
        try {
            $media = MediaStorage::find($id);

            if ($media) {
                self::removeData($media);
                return self::successResponse('Deleted successfully', ['id' => $media->id]);
            } else {
                return self::errorResponse('Media not found');
            }
        } catch (Exception $e) {
            return self::errorResponse('Error deleting media', $e);
        }
    }

    private static function createMedia(UploadedFile $file, $model, $customFolder = null)
    {
        try {
            $fileName = self::generateFileName($model, $file->getClientOriginalExtension());
            $folder = $model . ($customFolder ? '/' . $customFolder : '');
            $directory = self::$directory . ($folder);

            $media = MediaStorage::create([
                'model_name' => $model,
                'full_name' => $fileName,
                'extension' => $file->getClientOriginalExtension(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'public_path' => url("storage/{$directory}/{$fileName}"),
                'storage_path' => $file->storeAs($directory, $fileName, 'public'),
            ]);
            return self::successResponse('Created successfully', ['id' => $media->id]);
        } catch (FileNotFoundException $e) {
            return self::errorResponse('File not found: ' . $e->getMessage(), $e);
        } catch (FilesystemException $e) {
            return self::errorResponse('Filesystem exception: ' . $e->getMessage(), $e);
        } catch (Exception $e) {
            return self::errorResponse('Error creating media', $e->getMessage());
        }
    }

    private static function updateMedia(UploadedFile $file, $previewId, $model, $customFolder = null)
    {
        try {
            $folder = $model . ($customFolder ? '/' . $customFolder : '');
            $directory = self::$directory . ($folder);

            $existingMedia = MediaStorage::findOrFail($previewId);
            Storages::disk('public')->delete($existingMedia->storage_path);
            $fileName = self::generateFileName($model, $file->getClientOriginalExtension());

            $existingMedia->update([
                'full_name' => $fileName,
                'extension' => $file->getClientOriginalExtension(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'public_path' => url("storage/{$directory}/{$fileName}"),
                'storage_path' => $file->storeAs($directory, $fileName, 'public'),
            ]);

            return self::successResponse('Updated successfully', ['id' => $existingMedia->id]);
        } catch (ModelNotFoundException $e) {
            return self::createMedia($file, $model);
        } catch (FileNotFoundException $e) {
            return self::errorResponse('File not found: ' . $e->getMessage(), $e);
        } catch (FilesystemException $e) {
            return self::errorResponse('Filesystem exception: ' . $e->getMessage(), $e);
        } catch (Exception $e) {
            return self::errorResponse('Error uploading media', $e->getMessage());
        }
    }

    private static function removeData($media)
    {
        Storages::disk('public')->delete($media->storage_path);
        $targetPath = self::$directory . ($media->model_name);

        $parentPath = dirname($media->storage_path);
        // If the directory is not the root and is empty after removing the file, remove the directory
        if ($parentPath !== '.' && $parentPath !== $targetPath && count(Storages::disk('public')->files($parentPath)) === 0) {
            $data = self::removeDirectory($parentPath);
        }
        $media->delete();
    }

    private static function removeDirectory($directory)
    {
        if (Storages::exists($directory)) {
            Storages::disk('public')->deleteDirectory($directory);
        }
        return true;
    }

    private static function generateFileName($model, $extension = 'jpg')
    {
        $timestamp = now()->timestamp;
        $randomString = bin2hex(random_bytes(4));

        return $model . '-' . $timestamp . '_' . $randomString . '.' . $extension;
    }

    private static function successResponse($message, $data = null)
    {
        return [
            'status' => 1,
            'message' => $message,
            'style' => 'success',
            'data' => $data ?? [],
        ];
    }

    private static function errorResponse($message, $exception = null)
    {
        if ($exception !== null) {
            Log::error($message . ': ' . $exception->getMessage());
            $message = $message . ': ' . $exception->getMessage();
        }

        return [
            'status' => 400,
            'message' => $message,
            'style' => 'danger',
            'data' => [],
        ];
    }

    private static function folderNameGenerate($folder)
    {
        return Str::lower(Str::slug($folder, '_'));
    }
}
