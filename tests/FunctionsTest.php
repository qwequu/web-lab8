<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../functions.php";

class FunctionsTest extends TestCase
{
    public function testTotalSets()
    {
        $this->assertEquals(120, total_sets(60, 2));
    }

    public function testOrderTotal()
    {
        // 60 працівників * 2 комплекти * 1250 грн
        $this->assertEquals(150000, order_total(60, 2, 1250));
    }

    public function testOrderTotalWithZeroEmployees()
    {
        $this->assertEquals(0, order_total(0, 2, 1250));
    }

    public function testOrderTotalNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        order_total(-5, 2, 1250);
    }

    public function testValidPhone()
    {
        $this->assertTrue(is_valid_phone("+380671112233"));
        $this->assertTrue(is_valid_phone("0671112233"));
    }

    public function testInvalidPhone()
    {
        $this->assertFalse(is_valid_phone("123"));
        $this->assertFalse(is_valid_phone("abc"));
    }
}
