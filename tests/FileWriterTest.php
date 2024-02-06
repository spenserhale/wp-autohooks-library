<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\FileWriter;

class FileWriterTest extends TestCase
{
    private string $newFile = __DIR__ . '/file-writer-test-new.php';
    private string $existingFile = __DIR__ . '/file-writer-test-existing.php';

    protected function setUp(): void
    {
        parent::setUp();

        if(is_file($this->newFile)) {
            unlink($this->newFile);
        }
        if(is_file($this->existingFile)) {
            unlink($this->existingFile);
        }

        $contents = "<?php
//=== Start AutoHooks Generated Section ===
add_action('cli_init', 'SH\\AutoHook\\Examples\\ExamplesClass::registerCommand', 9, 0);
//=== End AutoHooks Generated Section ===
";

        // Create a file with content
        file_put_contents($this->existingFile, $contents);
    }

    public function testWriteExistingFile(): void
    {
        $hooks = 'add_action(\'init\', \'SH\AutoHook\Examples\ExamplesClass::boot\', 99, 0);';

        FileWriter::write($hooks, $this->existingFile);
        self::assertFileExists($this->existingFile);

        $contents = file_get_contents($this->existingFile);
        self::assertEquals(1, substr_count($contents, FileWriter::START_COMMENT));
        self::assertEquals(1, substr_count($contents, FileWriter::END_COMMENT));
        self::assertEquals(1, substr_count($contents, $hooks));
    }

    public function testWriteNewFile(): void
    {
        $hooks = 'add_action(\'init\', \'SH\AutoHook\Examples\ExamplesClass::boot\', 99, 0);';

        FileWriter::write($hooks, $this->newFile);
        self::assertFileExists($this->newFile);

        $contents = file_get_contents($this->newFile);
        self::assertStringContainsString(FileWriter::START_COMMENT, $contents);
        self::assertStringContainsString(FileWriter::END_COMMENT, $contents);
        self::assertStringContainsString($hooks, $contents);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if(is_file($this->newFile)) {
            unlink($this->newFile);
        }
        if(is_file($this->existingFile)) {
            unlink($this->existingFile);
        }
    }
}
