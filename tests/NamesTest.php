<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\OrdnanceSurvey\Names\Tests;

use BeastBytes\OrdnanceSurvey\Names\Names;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NamesTest extends TestCase
{
    private const GAZETTEER_ENTRY = 'GAZETTEER_ENTRY';

    private static string $apiKey;

    public static function setUpBeforeClass(): void
    {
        self::$apiKey = require __DIR__ . '/Support/apiKey.php';
    }

    public static function findProvider(): Generator
    {
        foreach ([
            ['query' => 'Northampton'],
            ['query' => 'Bradford'],
            ['query' => 'NN12 6JN'],
        ] as $data) {
            yield $data['query'] => $data;
        }
    }

    public static function nearestProvider(): Generator
    {
        foreach ([
            'Heathrow' => ['point' => ['lat' => 51.47121514468652, 'lon' => -0.45364817429284376]],
            'Winter Gardens, Blackpool' =>  ['point' => ['lat' => 53.8171606460015, 'lon' => -3.0510696987209664]],
            'Canterbury Cathedral' => ['point' => ['lat' => 51.27989675153937, 'lon' => 1.083149997917232]],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[dataProvider('findProvider')]
    public function testFind(string $query): void
    {
        $result = Names::find(self::$apiKey, $query);

        $this->assertIsArray($result);
        $this->arrayHasKey(self::GAZETTEER_ENTRY, $result[0]);
        $this->assertIsArray($result[0][self::GAZETTEER_ENTRY]);
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[dataProvider('nearestProvider')]
    public function testNearest(array $point): void
    {
        $result = Names::nearest(self::$apiKey, $point);

        $this->assertIsArray($result);
        $this->arrayHasKey(self::GAZETTEER_ENTRY, $result[0]);
        $this->assertIsArray($result[0][self::GAZETTEER_ENTRY]);
    }
}
