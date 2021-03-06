<?php
declare(strict_types=1);

use \ParagonIE\ConstantTime\Hex;
use \ParagonIE\MultiFactor\FIDOU2F;

/**
 * Class FIDOU2FTest
 */
class FIDOU2FTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the TOPT trait via the FIDOU2F class
     *
     * Test vectors from RFC 6238
     */
    public function testTOTP()
    {
        $seed = Hex::decode(
            "3132333435363738393031323334353637383930"
        );
        $seed32 = Hex::decode(
            "3132333435363738393031323334353637383930" .
            "313233343536373839303132"
        );
        // Seed for HMAC-SHA512 - 64 bytes
        $seed64 = Hex::decode(
            "3132333435363738393031323334353637383930" .
            "3132333435363738393031323334353637383930" .
            "3132333435363738393031323334353637383930" .
            "31323334"
        );

        $testVectors = [
            [
                'time' =>
                    59,
                'outputs' => [
                    'sha1' =>
                        '94287082',
                    'sha256' =>
                        '46119246',
                    'sha512' =>
                        '90693936'
                ]
            ], [
                'time' =>
                    1111111109,
                'outputs' => [
                    'sha1' =>
                        '07081804',
                    'sha256' =>
                        '68084774',
                    'sha512' =>
                        '25091201'
                ]
            ], [
                'time' =>
                    1111111111,
                'outputs' => [
                    'sha1' =>
                        '14050471',
                    'sha256' =>
                        '67062674',
                    'sha512' =>
                        '99943326'
                ]
            ], [
                'time' =>
                    1234567890,
                'outputs' => [
                    'sha1' =>
                        '89005924',
                    'sha256' =>
                        '91819424',
                    'sha512' =>
                        '93441116'
                ]
            ], [
                'time' =>
                    2000000000,
                'outputs' => [
                    'sha1' =>
                        '69279037',
                    'sha256' =>
                        '90698825',
                    'sha512' =>
                        '38618901'
                ]
            ]
        ];
        if (PHP_INT_SIZE > 4) {
            // 64-bit systems only:
            $testVectors[] = [
                'time' =>
                    20000000000,
                'outputs' => [
                    'sha1' =>
                        '65353130',
                    'sha256' =>
                        '77737706',
                    'sha512' =>
                        '47863826'
                ]
            ];
        }

        $fido = new FIDOU2F();

        foreach ($testVectors as $test) {
            $this->assertSame(
                $test['outputs']['sha1'],
                $fido->getTOTPCode($seed, $test['time'], 0, 30, 8, 'sha1')
            );

            $this->assertSame(
                $test['outputs']['sha256'],
                $fido->getTOTPCode($seed32, $test['time'], 0, 30, 8, 'sha256')
            );

            $this->assertSame(
                $test['outputs']['sha512'],
                $fido->getTOTPCode($seed64, $test['time'], 0, 30, 8, 'sha512')
            );
        }
    }
}
