<?php

use App\Models\BibleStudy;
use App\Models\StudySeries;
use App\Support\StudyStorage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Move images from the id-based folders to the series / passage folders:
     * study images from images/series_{id}/study_{id} to images/{series}/{book}_{passage} (e.g. images/john_2026/jn_01.01-18),
     * series images from images/study-series/series_{id} to images/{series} (e.g. images/john_2026).
     * Files that aren't in the old folder are left alone; the default images stay in images/study-series.
     */
    public function up(): void
    {
        $disk = Storage::disk('public');

        foreach (BibleStudy::with('series.book', 'book')->get() as $study) {
            StudyStorage::move($disk, array_values($study->image_links ?? []), $this->oldStudyDirectory($study), $study->imageDirectory());
        }

        foreach (StudySeries::with('book')->get() as $series) {
            StudyStorage::move($disk, array_values($series->images ?? []), "images/study-series/series_{$series->id}", $series->imageDirectory());
        }
    }

    /**
     * Move the images back to the id-based folders.
     */
    public function down(): void
    {
        $disk = Storage::disk('public');

        foreach (BibleStudy::with('series.book', 'book')->get() as $study) {
            StudyStorage::move($disk, array_values($study->image_links ?? []), $study->imageDirectory(), $this->oldStudyDirectory($study));
        }

        foreach (StudySeries::with('book')->get() as $series) {
            StudyStorage::move($disk, array_values($series->images ?? []), $series->imageDirectory(), "images/study-series/series_{$series->id}");
        }
    }

    private function oldStudyDirectory(BibleStudy $study): string
    {
        $series = $study->study_series_id ? "series_{$study->study_series_id}" : 'series_none';

        return "images/{$series}/study_{$study->id}";
    }
};
