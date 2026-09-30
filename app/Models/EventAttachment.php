<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventAttachment extends Model
{
    protected $table = 'event_attachments';

    protected $fillable = [
        'event_id',
        'type',
        'document_name',
        'locale',
    ];

    public const TYPES = ['document', 'media']; // document: pdf, docx | media: png, jpg, mp4
    public const LOCALES = ['en_CA', 'fr_CA']; // Documents only; media have no locale and show in both languages

    /**
     * Get parent ownership relationship context.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Path on the private "local" disk: documents/events/{category}/{start date}/{document_name},
     * e.g. documents/events/conference/2026-11-20/fall_conference_schedule.pdf
     * Usage: Storage::disk('local')->response($attachment->storage_path)
     */
    protected function storagePath(): Attribute
    {
        return Attribute::get(fn () => $this->event->documentDirectory() . '/' . $this->document_name);
    }

    /**
     * Lowercase file extension, e.g. "pdf".
     * Usage: $attachment->extension
     */
    protected function extension(): Attribute
    {
        return Attribute::get(fn () => strtolower(pathinfo($this->document_name, PATHINFO_EXTENSION)));
    }

    /**
     * Readable name from the file name, e.g. "Fall conference schedule" from "fall_conference_schedule.pdf".
     * Usage: $attachment->display_name
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(fn () => ucfirst(str_replace(['_', '-'], ' ', pathinfo($this->document_name, PATHINFO_FILENAME))));
    }

    /**
     * Whether the file is a video (shown with a player instead of an image).
     */
    public function isVideo(): bool
    {
        return $this->extension === 'mp4';
    }

    /**
     * Font Awesome icon for the file type.
     * Usage: $attachment->icon
     */
    protected function icon(): Attribute
    {
        return Attribute::get(fn () => match ($this->extension) {
            'pdf' => 'fa-file-pdf text-red-500',
            'docx' => 'fa-file-word text-blue-500',
            'mp4' => 'fa-file-video text-purple-400',
            default => 'fa-file-image text-slate-200',
        });
    }

    /**
     * File size in the current language, e.g. "350 Ko" / "1,2 MB"; null when the file is missing.
     * Usage: $attachment->size_label
     */
    protected function sizeLabel(): Attribute
    {
        return Attribute::get(function () {
            $disk = Storage::disk('local');

            if (!$disk->exists($this->storage_path)) {
                return null;
            }

            $size = $disk->size($this->storage_path);
            $units = __('events/index.size_units');
            $unit = 0;

            while ($size >= 1024 && $unit < count($units) - 1) {
                $size /= 1024;
                $unit++;
            }

            // Whole bytes and kilobytes, one decimal from megabytes up
            $number = number_format($size, $unit >= 2 ? 1 : 0, __('events/index.decimal_separator'), ' ');

            return $number . ' ' . $units[$unit];
        });
    }

    /**
     * Link that opens (PDF, image, video) or downloads (DOCX) the file.
     * Usage: $attachment->url
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn () => route('event-attachments.show', $this));
    }

}
