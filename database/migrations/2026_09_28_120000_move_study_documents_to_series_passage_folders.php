<?php

use App\Support\StudyStorage;
use App\Models\StudyAttachment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Move attachment files from documents/series_{id}/{locale}/study_{id} to documents/{series}/{book}_{passage}
     * (e.g. documents/john_2026/jn_01.01-18). Files that aren't in the old folder are left alone.
     */
    public function up(): void
    {
        $this->moveFiles(fn ($attachment) => [$this->oldPath($attachment), $attachment->storage_path]);
    }

    /**
     * Move the files back to the id-based folders.
     */
    public function down(): void
    {
        $this->moveFiles(fn ($attachment) => [$attachment->storage_path, $this->oldPath($attachment)]);
    }

    private function moveFiles(callable $paths): void
    {
        $disk = Storage::disk('local');

        foreach (StudyAttachment::with('bibleStudy.series.book', 'bibleStudy.book')->get() as $attachment) {
            [$from, $to] = $paths($attachment);

            if ($from !== $to && $disk->exists($from)) {
                $disk->move($from, $to);
                StudyStorage::deleteEmptyFolders($disk, dirname($from));
            }
        }
    }

    private function oldPath(StudyAttachment $attachment): string
    {
        $study = $attachment->bibleStudy;
        $series = $study->study_series_id ? "series_{$study->study_series_id}" : 'series_none';

        return "documents/{$series}/{$attachment->locale}/study_{$study->id}/{$attachment->name_with_extension}";
    }
};
