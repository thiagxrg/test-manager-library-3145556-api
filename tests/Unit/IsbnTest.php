<?php

namespace Tests\Unit;

use Library\Domain\Isbn;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    public function test_accepts_thirteen_digits(): void
    {
        $isbn = new Isbn('9780307474728');
        $this->assertSame('9780307474728', $isbn->value());
    }

    public function test_rejects_non_numeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Isbn('978030747ABCD');
    }

    public function test_rejects_wrong_length(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Isbn('12345');
    }
}