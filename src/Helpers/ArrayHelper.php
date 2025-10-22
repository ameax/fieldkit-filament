<?php

namespace Ameax\FieldkitFilament\Helpers;

class ArrayHelper
{
    public static function fromString(string $subject, bool $removeEmptyLine = false): array
    {
        $data = [];
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $subject) ?: [] as $line) {
            if (empty($line) && $removeEmptyLine) {
                continue;
            }

            $data[] = $line;
        }

        return $data;
    }
}
