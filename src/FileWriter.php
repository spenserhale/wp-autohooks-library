<?php

namespace SH\AutoHook;

class FileWriter
{
    public const START_COMMENT = "//=== Start AutoHooks Generated Section ===\n";
    public const END_COMMENT = "//=== End AutoHooks Generated Section ===\n";

    public static function write(string $hooks, string $path): int|false
    {
        if(is_file($path)) {
            $contents = file_get_contents($path);
            if ( ! $contents) {
                return false;
            }

            $startPos = strpos($contents, self::START_COMMENT);
            $endPos   = strpos($contents, self::END_COMMENT);

            if ($startPos !== false && $endPos !== false) {
                $contents = substr_replace(
                    $contents,
                    $hooks,
                    $startPos + strlen(self::START_COMMENT),
                    $endPos - $startPos - strlen(self::START_COMMENT)
                );
            } else {
                $contents .= PHP_EOL.self::START_COMMENT.$hooks.self::END_COMMENT;
            }
        } else {
            $contents = '<?php'.PHP_EOL.self::START_COMMENT.$hooks.self::END_COMMENT;
        }

        return file_put_contents($path, $contents, LOCK_EX);
    }
}