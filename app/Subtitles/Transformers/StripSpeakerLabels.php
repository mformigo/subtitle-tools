<?php

namespace App\Subtitles\Transformers;

class StripSpeakerLabels extends CueTransformer
{
    public function transformLines(array $lines): array
    {
        $transformedLines = [];

        foreach ($lines as $line) {
            $transformedLines[] = $this->stripSpeakerLabel($line);
        }

        return array_values(
            array_filter($transformedLines)
        );
    }

    protected function stripSpeakerLabel($line): string
    {
        if (strpos($line, ':') === false) {
            return $line;
        }

        [$maybeSpeakerLabel, $dialogue] = explode(':', $line, 2);

        return $this->isSpeakerLabel($maybeSpeakerLabel)
            ? trim($dialogue)
            : $line;
    }

    protected function isSpeakerLabel($string): bool
    {
        // Strings without normal letters are not speaker labels. For example:
        // fully numeric strings (years: 1993), or different languages (好的)
        if (! preg_match('/[a-z]/i', $string)) {
            return false;
        }

        // Fully uppercase strings are almost always speaker labels
        if (strtoupper($string) === $string) {
            return true;
        }

        // OCR errors commonly produce speaker labels that are mostly uppercase,
        // but contain one or a few lowercase letters.
        //
        // Example: "WlSE MAN" (the uppercase I is OCR'd as a lowercase L)
        if ($this->isMostlyUppercase($string)) {
            return true;
        }

        return false;
    }

    protected function isMostlyUppercase($string): bool
    {
        // Remove everything that can not be uppercased (such as spaces, dashed)
        $string = preg_replace('/[^a-zA-Z]/', '', $string);

        $lowercaseCount = strlen(
            preg_replace('/[A-Z]/', '', $string)
        );

        $uppercaseCount = strlen($string) - $lowercaseCount;

        // Consider a string mostly uppercase if it has twice as
        // many uppercase letters than lowercase letters.
        return $uppercaseCount > $lowercaseCount * 2;
    }
}
