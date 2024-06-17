<?php
/**
 * The Nilfraud Project
 * @copyright  Copyright (c) Hybula B.V. (https://www.hybula.com)
 * @author     Nilfraud Development Team <development@hybula.com>
 * @copyright  2017-2024 Hybula B.V.
 * @license    MPL-2.0 License
 * @link       https://github.com/nilfraud/php-library
 */

declare(strict_types=1);

namespace Nilfraud\Helpers;

class Hash
{
    /**
     * WARNING DO NOT CHANGE!
     * @var string Prefix to use as salt when hashing.
     */
    protected static string $hashSalt = 'nilfraud-';
    /**
     * WARNING DO NOT CHANGE!
     * @var int Iterations to do for hashing.
     */
    protected static int $hashIterations = 7007;

    /**
     * Generates a hash from provided string of data.
     *
     * @param string $data
     * @return string Containing sha512 hash.
     */
    public static function generate(string $data): string
    {
        $data = str_replace(' ','', strtolower(trim($data)));
        for ($iteration = 0; $iteration < self::$hashIterations; $iteration++) {
            $data = hash('sha512', self::$hashSalt.$data.$iteration);
        }
        return $data;
    }

    /**
     * Generates bulk hashes from an array of strings.
     *
     * @param array $data An array with strings of data to hash.
     * @return array An array with sha512 hashes.
     */
    public static function generateBulk(array $data): array
    {
        return array_map(function($value) {
            return self::generate($value);
        }, $data);
    }
}
