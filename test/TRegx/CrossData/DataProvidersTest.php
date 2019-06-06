<?php
namespace TRegx\CrossData;

use PHPUnit\Framework\TestCase;

class DataProvidersTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCross()
    {
        // when
        $result = DataProviders::cross([[1], [2]], [['A'], ['B']]);

        // then
        $expected = [
            '[0,0]' => [1, 'A'],
            '[0,1]' => [1, 'B'],
            '[1,0]' => [2, 'A'],
            '[1,1]' => [2, 'B'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldKeyMapper()
    {
        // when
        $result = DataProviders::input([[1], [2]], [['A'], ['B']])
            ->keyMapper(function ($keys) {
                return join('+', $keys);
            })
            ->create();

        // then
        $expected = [
            '0+0' => [1, 'A'],
            '0+1' => [1, 'B'],
            '1+0' => [2, 'A'],
            '1+1' => [2, 'B'],
        ];
        $this->assertEquals($expected, $result);
    }
}