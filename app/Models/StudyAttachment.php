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

    public const LOCALES = ['en_CA', 'fr_CA'];
    public const TYPES = ['question_sheet', 'lecture', 'other'];
    public const EXTENSIONS = ['pdf', 'docx'];
    public const PUBLIC_TYPES = ['question_sheet']; // Open to everyone; the other types need a User role or above

    /**
     * Whether this attachment is open to everyone, or needs a User role or above.
     */
    public function isPublic(): bool
    {
        return in_array($this->type, self::PUBLIC_TYPES);
    }

    /**
     * File name with its extension, e.g. "jn_03.01-21.q.fr.pdf".
     * (Not "fileName()": PHP method names ignore case, so that would clash with the "filename" column.)
     * Usage: $attachment->name_with_extension
     */
    protected function nameWithExtension(): Attribute
    {
        return Attribute::get(fn () => "{$this->filename}.{$this->extension}");
    }

    /**
     * Path on the private "local" disk: documents/series_{id}/{locale}/study_{id}/{filename}.{extension}
     * Usage: Storage::disk('local')->download($attachment->storage_path)
     */
    protected function storagePath(): Attribute
    {
        return Attribute::get(fn () => $this->bibleStudy->documentDirectory($this->locale) . '/' . $this->name_with_extension);
    }

}
