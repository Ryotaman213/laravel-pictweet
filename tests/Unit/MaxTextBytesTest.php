<?php

namespace Tests\Unit;

use App\Rules\MaxTextBytes;
use PHPUnit\Framework\TestCase;

class MaxTextBytesTest extends TestCase
{
    public function test_text_within_the_database_byte_limit_is_accepted(): void
    {
        $errors = [];
        (new MaxTextBytes)->validate('text', str_repeat('a', 65535), function (string $message) use (&$errors): void {
            $errors[] = $message;
        });

        $this->assertSame([], $errors);
    }

    public function test_text_over_the_database_byte_limit_is_rejected(): void
    {
        $errors = [];
        (new MaxTextBytes)->validate('text', str_repeat('a', 65536), function (string $message) use (&$errors): void {
            $errors[] = $message;
        });

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('65535 bytes', $errors[0]);
    }
}
