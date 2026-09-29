<?php

namespace App\Console\Commands;

use App\Models\BibleStudy;
use App\Models\StudyAttachment;
use App\Models\StudySeries;
use App\Support\StudyStorage;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Puts study files back where the database expects them (images/{series}/{book}_{passage}, documents/{series}/{book}_{passage})
 * by copying them from the old id-based folders, then optionally clears out the leftovers.
 * Without options it only reports what it would do.
 */
class RepairStudyStorage extends Command
{
    protected $signature = 'study-storage:repair
                            {--apply : Copy missing files from the old id-based folders}
                            {--cleanup : Delete documents in the images folder and old folders whose files all have an identical copy}';

    protected $description = 'Restore study images and documents from the old id-based folders and remove leftovers';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $local = Storage::disk('local');

        $this->restore($public, $local);

        if ($this->option('cleanup')) {
            $this->cleanup($public, $local);
        }

        if (! $this->option('apply') && ! $this->option('cleanup')) {
            $this->line('Preview only. Run with --apply to copy, --cleanup to remove leftovers.');
        }

        return self::SUCCESS;
    }

    /**
     * Copy each file the database references from its old folder when it's missing from the new one.
     */
    private function restore(FilesystemAdapter $public, FilesystemAdapter $local): void
    {
        $missing = [];

        foreach (StudyAttachment::with('bibleStudy')->get() as $attachment) {
            $study = $attachment->bibleStudy;
            $old = "documents/{$this->oldSeries($study)}/{$attachment->locale}/study_{$study->id}/{$attachment->name_with_extension}";
            $missing[] = [$local, $old, $attachment->storage_path];
        }

        foreach (BibleStudy::all() as $study) {
            foreach (array_values($study->image_links ?? []) as $name) {
                $missing[] = [$public, "images/{$this->oldSeries($study)}/study_{$study->id}/{$name}", "{$study->imageDirectory()}/{$name}"];
            }
        }

        foreach (StudySeries::all() as $series) {
            foreach (array_values($series->images ?? []) as $name) {
                $missing[] = [$public, "images/study-series/series_{$series->id}/{$name}", "{$series->imageDirectory()}/{$name}"];
            }
        }

        $restored = 0;
        $lost = [];

        foreach ($missing as [$disk, $old, $new]) {
            if ($disk->exists($new)) {
                continue;
            }

            if (! $disk->exists($old)) {
                $lost[] = $new;
                continue;
            }

            $this->option('apply') ? $disk->copy($old, $new) : $this->line("  would copy {$old} -> {$new}");
            $restored++;
        }

        $this->info(($this->option('apply') ? 'Restored' : 'To restore').": {$restored}");

        if ($lost) {
            $this->warn('Missing with no copy in the old folders (upload these again): '.count($lost));
            foreach ($lost as $path) {
                $this->line("  {$path}");
            }
        }
    }

    /**
     * Delete documents from the images folder, and files in the old folders that have an identical copy in the new ones.
     * Files without a copy are kept and listed.
     */
    private function cleanup(FilesystemAdapter $public, FilesystemAdapter $local): void
    {
        $documents = array_filter($public->allFiles('images'), fn ($path) => preg_match('/\.(docx?|pdf)$/i', $path));
        $public->delete($documents);
        $this->info('Documents removed from images: '.count($documents));

        $oldFolders = [
            [$public, array_merge(
                array_filter($public->directories('images'), fn ($dir) => str_starts_with(basename($dir), 'series_')),
                array_filter($public->directories('images/study-series'), fn ($dir) => str_starts_with(basename($dir), 'series_')),
            )],
            [$local, array_filter($local->directories('documents'), fn ($dir) => str_starts_with(basename($dir), 'series_'))],
        ];

        foreach ($oldFolders as [$disk, $folders]) {
            $kept = $this->checksums($disk, $folders);
            $deleted = 0;

            foreach ($folders as $folder) {
                foreach ($disk->allFiles($folder) as $path) {
                    if (isset($kept[$disk->checksum($path)])) {
                        $disk->delete($path);
                        $deleted++;
                    } else {
                        $this->warn("  kept (no copy in the new folders): {$path}");
                    }
                }

                foreach (array_reverse($disk->allDirectories($folder)) as $directory) {
                    StudyStorage::deleteEmptyFolders($disk, $directory);
                }
                StudyStorage::deleteEmptyFolders($disk, $folder);
            }

            $this->info('Old folder files removed: '.$deleted);
        }
    }

    /**
     * Checksums of every file on the disk outside the old folders, keyed for lookup.
     */
    private function checksums(FilesystemAdapter $disk, array $oldFolders): array
    {
        $sums = [];

        foreach ($disk->allFiles() as $path) {
            foreach ($oldFolders as $folder) {
                if (str_starts_with($path, "{$folder}/")) {
                    continue 2;
                }
            }

            $sums[$disk->checksum($path)] = true;
        }

        return $sums;
    }

    private function oldSeries(BibleStudy $study): string
    {
        return $study->study_series_id ? "series_{$study->study_series_id}" : 'series_none';
    }
}
