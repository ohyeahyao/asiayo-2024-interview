<?php

declare(strict_types=1);

use App\Constants\RouteNames;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class CheckAndTransformOrderControllerTest extends TestCase
{
    public static function invalidOrderDataAnd400ErrorProvider(): iterable
    {
        return [
            'name 包含非英文字母' => [
                [
                    'id'      => 'A0000001',
                    'name'    => '嗨! John',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 1050,
                    'currency' => 'TWD',
                ],
                [
                    'name' => ['Name contains non-English characters'],
                ],
            ],
            'name 每個單字首字母非大寫' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'John x',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 1050,
                    'currency' => 'TWD',
                ],
                [
                    'name' => ['Name is not capitalized'],
                ],
            ],
            'name 每個單字首字母非大寫 + 包含非英文字母' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'John x 嗨!',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 1050,
                    'currency' => 'TWD',
                ],
                [
                    'name' => ['Name contains non-English characters', 'Name is not capitalized'],
                ],
            ],
            'price 訂單金額超過 2000' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'John Yao',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                [
                    'price' => ['Price is over 2000'],
                ],
            ],
            'currency 貨幣格式若非 TWD 或 USD' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'John Yao',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 1050,
                    'currency' => 'JPY',
                ],
                [
                    'currency' => ['Currency format is wrong'],
                ],
            ],
        ];
    }

    #[DataProvider('invalidOrderDataAnd400ErrorProvider')]
    public function testInvalidOrderDataAnd400Error($payload, $expectedErrors): void
    {
        $response = $this->json('post', route(RouteNames::ORDER_CHECK_AND_TRANSFORM), $payload);
        $response->assertStatus(400)
        ->assertJsonStructure(['message', 'errors'])
        ->assertJson([
            'message' => 'Validated failed',
            'errors'  => $expectedErrors,
        ]);
        ;
    }

    public static function invalidDataProvider(): iterable
    {
        return [
            'ID missing' => [
                [
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                ['id'],
            ],
            'Name missing' => [
                [
                    'id'      => 'A0000001',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                ['name'],
            ],
            'City missing' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                ['address.city'],
            ],
            'District missing' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'   => 'taipei-city',
                        'street' => 'fuxing-south-road',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                ['address.district'],
            ],
            'Street missing' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                    ],
                    'price'    => 2050,
                    'currency' => 'TWD',
                ],
                ['address.street'],
            ],
            'Price missing' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'currency' => 'TWD',
                ],
                ['price'],
            ],
            'Currency missing' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price' => 2050,
                ],
                ['currency'],
            ],
            'Price is not numeric' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => '2050xx',
                    'currency' => 'TWD',
                ],
                ['price'],
            ],
        ];
    }
    
    #[DataProvider('invalidDataProvider')]
    public function testInvalidData($payload, $expectedErrors): void
    {
        $response = $this->json('post', route(RouteNames::ORDER_CHECK_AND_TRANSFORM), $payload);
        $response->assertStatus(422)->assertJsonValidationErrors($expectedErrors);
    }

    public static function validDataProvider(): iterable
    {
        return [
            'TWD' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 2000,
                    'currency' => 'TWD',
                ],
                [
                    'price'    => 2000,
                    'currency' => 'TWD',
                ],
            ],
            'USD 當貨幣為 USD 時，需修改 price 金額乘上固定匯率 31 元，並且將 currency 改為 TWD' => [
                [
                    'id'      => 'A0000001',
                    'name'    => 'Melody Holiday Inn',
                    'address' => [
                        'city'     => 'taipei-city',
                        'district' => 'da-an-district',
                        'street'   => 'fuxing-south-road',
                    ],
                    'price'    => 1000,
                    'currency' => 'USD',
                ],
                [
                    'price'    => 31000,
                    'currency' => 'TWD',
                ],
            ],
        ];
    }
    
    #[DataProvider('validDataProvider')]
    public function testValidData($payload, $expected): void
    {
        $response = $this->json('post', route(RouteNames::ORDER_CHECK_AND_TRANSFORM), $payload);
        $response->assertStatus(200)
        ->assertJsonStructure(
            [
                'id',
                'name',
                'address' => [
                    'city',
                    'district',
                    'street',
                ],
                'price',
                'currency',
            ]
        )->assertJson($expected);
    }
}
