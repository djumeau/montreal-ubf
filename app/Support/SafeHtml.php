<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Keeps a short list of formatting tags in text typed by admins (event descriptions) and removes everything else,
 * so the result can be printed with {!! !!}. Uses PHP's built-in DOM extension (no Composer package).
 * Usage: SafeHtml::clean($event->current_description)
 */
final class SafeHtml
{
    // Tags kept; any other tag is removed but its text stays
    private const TAGS = ['p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u', 'a', 'h3', 'h4', 'blockquote'];

    // Tags removed along with their content
    private const DROPPED = ['script', 'style', 'iframe', 'object', 'embed', 'template', 'form', 'svg', 'math'];

    // Attributes kept per tag; all others (onclick, style, class...) are removed
    private const ATTRIBUTES = ['a' => ['href']];

    /**
     * Text without any tag keeps its line breaks; text with tags is cleaned to the allowed ones.
     */
    public static function clean(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return '';
        }

        if (!str_contains($text, '<')) {
            return nl2br(e($text));
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true); // Loose HTML (e.g. unclosed tags) is fine
        $document->loadHTML('<meta charset="utf-8"><div>' . $text . '</div>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('div')->item(0);
        self::cleanChildren($root);

        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return trim($html);
    }

    private static function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            // Comments and anything that isn't an element
            if (!$child instanceof DOMElement) {
                $node->removeChild($child);
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROPPED, true)) {
                $node->removeChild($child);
                continue;
            }

            self::cleanChildren($child);

            // Tag not allowed: keep its (already cleaned) content in its place
            if (!in_array($tag, self::TAGS, true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                if (!in_array(strtolower($attribute->name), self::ATTRIBUTES[$tag] ?? [], true)) {
                    $child->removeAttribute($attribute->name);
                }
            }

            if ($tag === 'a') {
                self::cleanLink($child);
            }
        }
    }

    /**
     * Links: web, email and site-relative addresses only (no "javascript:"); outside links open in a new tab.
     */
    private static function cleanLink(DOMElement $link): void
    {
        $href = trim($link->getAttribute('href'));

        if (!preg_match('~^(https?://|mailto:|/|#)~i', $href)) {
            $link->removeAttribute('href');
            return;
        }

        if (preg_match('~^https?://~i', $href)) {
            $link->setAttribute('target', '_blank');
            $link->setAttribute('rel', 'noopener noreferrer');
        }
    }
}
