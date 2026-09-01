<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Answers questions about an uploaded HTML document without ever rewriting it.
 *
 * The document is parsed into a throwaway DOM purely to decide two things:
 * whether it already ships its own navigation, and whether it has enough
 * structure to be worth generating one for. The parsed DOM is never serialised
 * back out - DOMDocument::saveHTML() normalises self-closing tags, re-encodes
 * entities and can corrupt inline <script> contents, which would silently
 * damage a working client document. What gets served is always the original
 * bytes, with at most one <script> tag appended.
 */
class HtmlDocumentAnalyzer
{
    /** Minimum targeted controls before an aside/nav counts as real navigation. */
    private const MIN_NAV_CONTROLS = 2;

    /** Minimum sections before a generated sidebar is worth building. */
    private const MIN_SECTIONS = 2;

    /** Elements whose descendants are never real document content. */
    private const INERT_ANCESTORS = ['script', 'style', 'template'];

    /**
     * @return array{owns_navigation:bool, section_count:int, tier:?string, should_inject:bool}
     */
    public function analyze(string $html): array
    {
        $empty = [
            'owns_navigation' => false,
            'section_count'   => 0,
            'tier'            => null,
            'should_inject'   => false,
        ];

        if (trim($html) === '') {
            return $empty;
        }

        $xpath = $this->parse($html);

        if (!$xpath) {
            return $empty;
        }

        $ownsNavigation = $this->ownsNavigation($xpath);
        [$tier, $count] = $this->sectionCandidates($xpath);

        return [
            'owns_navigation' => $ownsNavigation,
            'section_count'   => $count,
            'tier'            => $count >= self::MIN_SECTIONS ? $tier : null,
            'should_inject'   => !$ownsNavigation && $count >= self::MIN_SECTIONS,
        ];
    }

    /**
     * Parse into a throwaway DOM. Client HTML is not guaranteed well-formed, so
     * libxml errors are swallowed rather than surfaced.
     */
    private function parse(string $html): ?DOMXPath
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument();
            $loaded   = $document->loadHTML(
                '<?xml encoding="utf-8" ?>' . $html,
                LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
            );

            return $loaded ? new DOMXPath($document) : null;
        } catch (\Throwable) {
            return null;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    /**
     * A document owns its navigation when an <aside> or <nav> holds two or more
     * controls pointing at in-document targets.
     */
    private function ownsNavigation(DOMXPath $xpath): bool
    {
        $containers = $xpath->query('//aside | //nav');

        foreach ($containers as $container) {
            $controls = $xpath->query('.//a | .//button', $container);
            $targeted = 0;

            foreach ($controls as $control) {
                if ($this->isTargetedControl($control)) {
                    $targeted++;
                }
            }

            if ($targeted >= self::MIN_NAV_CONTROLS) {
                return true;
            }
        }

        return false;
    }

    private function isTargetedControl(DOMNode $control): bool
    {
        if (!$control instanceof DOMElement) {
            return false;
        }

        if ($control->hasAttribute('data-target') && $control->getAttribute('data-target') !== '') {
            return true;
        }

        $href = $control->getAttribute('href');

        return str_starts_with($href, '#') && strlen($href) > 1;
    }

    /**
     * Count section candidates in priority order, returning the tier that won.
     *
     * @return array{0:?string, 1:int}
     */
    private function sectionCandidates(DOMXPath $xpath): array
    {
        $withId      = 0;
        $withHeading = 0;

        foreach ($xpath->query('//section') as $section) {
            if ($this->hasInertAncestor($section)) {
                continue;
            }

            if ($section->getAttribute('id') !== '') {
                $withId++;
            } elseif ($this->firstHeadingText($xpath, $section) !== null) {
                $withHeading++;
            }
        }

        $sections = $withId + $withHeading;

        if ($sections >= self::MIN_SECTIONS) {
            return [$withId > 0 && $withHeading === 0 ? 'section-id' : 'section-heading', $sections];
        }

        // Fall back to headings in document order.
        $headings = 0;

        foreach ($xpath->query('//h2') as $heading) {
            if (!$this->hasInertAncestor($heading) && trim($heading->textContent) !== '') {
                $headings++;
            }
        }

        if ($headings >= self::MIN_SECTIONS) {
            return ['heading', $headings];
        }

        return [null, max($sections, $headings)];
    }

    private function firstHeadingText(DOMXPath $xpath, DOMNode $section): ?string
    {
        foreach ($xpath->query('.//h1 | .//h2 | .//h3', $section) as $heading) {
            $text = trim($heading->textContent);

            if ($text !== '') {
                return $text;
            }
        }

        return null;
    }

    /**
     * DOMDocument parses <template> children as ordinary elements, so anything
     * inside one - or inside a script or style block - has to be excluded
     * explicitly.
     */
    private function hasInertAncestor(DOMNode $node): bool
    {
        for ($current = $node->parentNode; $current !== null; $current = $current->parentNode) {
            if ($current instanceof DOMElement
                && in_array(strtolower($current->nodeName), self::INERT_ANCESTORS, true)) {
                return true;
            }
        }

        return false;
    }
}
