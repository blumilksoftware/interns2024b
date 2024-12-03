<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Rules\Regon;
use Illuminate\Support\Facades\Lang;
use PHPUnit\Framework\TestCase;

class RegonRuleTest extends TestCase
{
    public function testValidShortRegon(): void
    {
        Lang::shouldReceive("get");

        $this->assertTrue($this->validateRegon("304608090"));
        $this->assertTrue($this->validateRegon("743428671"));
    }

    public function testShortRegonWithInvalidChecksum(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.regon.invalid_checksum");

        $this->assertFalse($this->validateRegon("304608091"));
    }

    public function testShortRegonWithInvalidLength(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.regon.invalid_length");

        $this->assertFalse($this->validateRegon("12345"));
    }

    public function testValidLongRegon(): void
    {
        Lang::shouldReceive("get");

        $this->assertTrue($this->validateRegon("30460809063960"));
        $this->assertTrue($this->validateRegon("74342867138461"));
    }

    public function testLongRegonWithInvalidChecksum(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.regon.invalid_checksum");

        $this->assertFalse($this->validateRegon("30460809163960"));
        $this->assertFalse($this->validateRegon("30460809063961"));
    }

    public function testLongRegonWithInvalidLength(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.regon.invalid_length");

        $this->assertFalse($this->validateRegon("1234567851"));
    }

    public function testNonNumericRegon(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.regon.digits_only");

        $this->assertFalse($this->validateRegon("abcdefghi"));
        $this->assertFalse($this->validateRegon("12345abc9"));
    }

    protected function validateRegon(string $value): bool
    {
        $failed = false;
        $rule = new Regon();

        $rule->validate("regon", $value, function ($err) use (&$failed): void {
            $failed = true;
        });

        return !$failed;
    }
}
