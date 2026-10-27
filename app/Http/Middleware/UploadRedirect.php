<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadRedirect
{
    /**
     * Uploads sent by the page itself to show their progress (uploadForm in app.js) get the address of the redirect
     * instead of the redirect: the browser then goes there itself, so the flashed status message and validation errors
     * are shown on that page rather than used up by the upload request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->hasHeader('X-Upload-Progress') || ! $response instanceof RedirectResponse) {
            return $response;
        }

        $json = new JsonResponse(['redirect' => $response->getTargetUrl()]);

        // Cookies set along with the redirect (e.g. the language) still reach the browser
        foreach ($response->headers->getCookies() as $cookie) {
            $json->headers->setCookie($cookie);
        }

        return $json;
    }
}
