<?php

namespace App\Http\Controllers;

use App\Support\HeroImages;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroImageController extends Controller
{
    // @desc Upload images of the home page hero (desktop and / or mobile); an image of the same name is replaced
    // @route POST /manage-home-page/hero-images
    public function store(Request $request): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $rules = [];
        foreach (HeroImages::TYPES as $type) {
            $rules[$type] = [
                'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . HeroImages::maxKilobytes(),
                // The ending of the name says which image of the slide it is: "montreal-sunset-desktop.jpg"
                function (string $attribute, mixed $file, Closure $fail) use ($type) {
                    if ($file instanceof UploadedFile && !$this->slug($file, $type)) {
                        $fail(__('dashboard/manage-home-page/index.name_must_end_with', ['suffix' => '-' . $type]));
                    }
                },
            ];
        }

        // At least one image: the message shows under the first drop zone
        [$first, $others] = [HeroImages::TYPES[0], array_slice(HeroImages::TYPES, 1)];
        array_unshift($rules[$first], 'required_without_all:' . implode(',', $others));

        $request->validateWithBag('uploadHeroImages', $rules, [
            $first . '.required_without_all' => __('dashboard/manage-home-page/index.images_none_chosen'),
        ]);

        $disk = Storage::disk(HeroImages::DISK);

        foreach (HeroImages::TYPES as $type) {
            if (!$request->hasFile($type)) {
                continue;
            }

            $file = $request->file($type);
            $slug = $this->slug($file, $type);

            // Remove the image being replaced (it may have another extension)
            $disk->delete(HeroImages::files($slug, $type));

            // Extension from the file's content, not from the name it was sent with
            $file->storeAs(HeroImages::DIRECTORY, "{$slug}-{$type}." . $file->extension(), HeroImages::DISK);
        }

        return back()->with('status', __('dashboard/manage-home-page/index.images_saved'));
    }

    // @desc Remove a slide of the home page hero: its desktop and mobile images
    // @route DELETE /manage-home-page/hero-images/{slide}
    public function destroy(Request $request, string $slide): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $files = HeroImages::files($slide);
        abort_if(empty($files), 404);

        Storage::disk(HeroImages::DISK)->delete($files);

        return back()->with('status', __('dashboard/manage-home-page/index.images_deleted'));
    }

    /**
     * Name of the slide, safe for any file system: "Montréal Sunset-desktop.JPG" gives "montreal-sunset".
     * Null when the file name doesn't end with "-desktop" / "-mobile" (the type given), or has nothing before it.
     */
    private function slug(UploadedFile $file, string $type): ?string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        if (!Str::endsWith(strtolower($name), '-' . $type)) {
            return null;
        }

        return Str::slug(substr($name, 0, -strlen('-' . $type))) ?: null;
    }
}
