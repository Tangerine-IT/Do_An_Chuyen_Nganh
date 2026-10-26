<?php

namespace Tests\Unit;

use Tests\TestCase;

class FormatVndTest extends TestCase
{
    public function test_it_formats_amounts_with_dot_separators_and_symbol(): void
    {
        $this->assertSame('12.500.000 ₫', format_vnd(12500000));
    }

    public function test_it_formats_zero_and_small_amounts(): void
    {
        $this->assertSame('0 ₫', format_vnd(0));
        $this->assertSame('999 ₫', format_vnd(999));
    }
}
