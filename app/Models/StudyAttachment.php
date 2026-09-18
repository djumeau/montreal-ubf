<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StudyAttachment extends Model
{
    protected $table = 'study_attachments';

    protected $fillable = [
        'bible_study_id',
        'locale',
        'type',
        'filename',
        'extension',
        'views',
    ];

    /**
     * Get parent ownership relationship context.
     */
    public function bibleStudy(): BelongsTo
    {
        return $this->belongsTo(BibleStudy::class, 'bible_study_id');
    }

    /**
     * Composite accessor payload mapping the dynamic database storage location path seamlessly.
     * Usage: Storage::download($attachment->storage_path)
     */
    protected function storagePath(): Attribute
    {
        return Attribute::get(function () {
            return "studies/{$this->type}/{$this->filename}.{$this->extension}";
        });
    }

}
