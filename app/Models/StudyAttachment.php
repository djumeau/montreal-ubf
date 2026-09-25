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
     * Group Bible Study question sheet (".gbs" in the name, e.g. jn_02.01-12.gbs.q.fr.pdf):
     * simpler questions that complement the standard question sheet.
     */
    public function isGroupStudy(): bool
    {
        return str_contains($this->filename, '.gbs');
    }

    /**
     * One-letter type shown after the file name on study cards, in the current locale:
     * "Q" (question sheet), "L" (lecture), "O" (other) / "A" (autre).
     * Usage: $attachment->type_code
     */
    protected function typeCode(): Attribute
    {
        return Attribute::get(fn () => __('dashboard/index.attachment_code_' . $this->type));
    }

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
