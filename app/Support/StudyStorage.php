<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;

/**
 * File moves for the series / passage folders, e.g. images/john_2026/jn_01.01-18 and documents/john_2026/jn_01.01-18.
 * Only the named files move or go: two studies of the same passage in one series share a folder,
 * and a series folder also holds its studies' folders.
 */
class StudyStorage
{
    /**
     * Move the named files from one folder to another on the same disk, then remove the old folders once empty.
     * Does nothing when the folders match; files missing from the old folder are skipped.
     */
    public static function move(FilesystemAdapter $disk, array $names, string $oldDirectory, string $newDirectory): void
    {
        if ($oldDirectory === $newDirectory) {
            return;
        }

        foreach ($names as $name) {
            if ($disk->exists("{$oldDirectory}/{$name}")) {
                $disk->move("{$oldDirectory}/{$name}", "{$newDirectory}/{$name}");
            }
        }

        self::deleteEmptyFolders($disk, $oldDirectory);
    }

    /**
     * Delete the named files from a folder, then remove the folders once empty.
     */
    public static function delete(FilesystemAdapter $disk, array $names, string $directory): void
    {
        foreach ($names as $name) {
            $disk->delete("{$directory}/{$name}");
        }

        self::deleteEmptyFolders($disk, $directory);
    }

    /**
     * Remove a folder, then its parents, while nothing is left in them.
     * Stops below the top folder ("images", "documents").
     */
    public static function deleteEmptyFolders(FilesystemAdapter $disk, string $directory): void
    {
        while (str_contains($directory, '/') && $disk->directoryExists($directory)) {
            if ($disk->files($directory) || $disk->directories($directory)) {
                return;
            }

            $disk->deleteDirectory($directory);
            $directory = dirname($directory);
        }
    }
}
