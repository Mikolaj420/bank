<?php

namespace Tests\Unit;

use App\Rules\Pesel;
use PHPUnit\Framework\TestCase;

class PeselRuleTest extends TestCase
{
    private function fails(string $value): bool
    {
        $failed = false;

        (new Pesel)->validate('pesel', $value, function () use (&$failed) {
            $failed = true;
        });

        return $failed;
    }

    public function test_accepts_valid_pesel(): void
    {
        // 44051401359 — poprawna cyfra kontrolna.
        $this->assertFalse($this->fails('44051401359'));
    }

    public function test_rejects_wrong_length(): void
    {
        $this->assertTrue($this->fails('123'));
        $this->assertTrue($this->fails('440514013590'));
    }

    public function test_rejects_non_numeric(): void
    {
        $this->assertTrue($this->fails('4405140135A'));
    }

    public function test_rejects_invalid_checksum(): void
    {
        $this->assertTrue($this->fails('44051401358'));
    }
}
