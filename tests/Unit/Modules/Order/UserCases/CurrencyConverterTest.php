<?php

declare(strict_types=1);

namespace Tests\Unit\UserCases;

use Modules\Order\ConversionStrategies\CurrencyStrategyFactory;
use Modules\Order\CurrencyConverterStrategyInterface;
use Modules\Order\UseCases\CurrencyConverter;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CurrencyConverter::class)]
final class CurrencyConverterTest extends TestCase
{
    public function testConvertAndDontCallFactory(): void
    {
        $data = [
            'id'      => 'A0000001',
            'name'    => 'Melody Holiday Inn',
            'address' => [
                'city'     => 'taipei-city',
                'district' => 'da-an-district',
                'street'   => 'fuxing-south-road',
            ],
            'price'    => 2050.0,
            'currency' => 'TWD',
        ];
        $mockFactory = $this->createMock(CurrencyStrategyFactory::class);
        $mockFactory->expects(self::never())->method(self::anything());

        $converter = new CurrencyConverter($mockFactory);   
        self::assertSame($data, $converter->convert($data)->toArray());
    }

    public function testUsdToTwd(): void
    {
        $data = [
            'id'      => 'A0000001',
            'name'    => 'Melody Holiday Inn',
            'address' => [
                'city'     => 'taipei-city',
                'district' => 'da-an-district',
                'street'   => 'fuxing-south-road',
            ],
            'price'    => 1000.0, 
            'currency' => 'USD',
        ];

        $expectedData = [
            'id'      => 'A0000001',
            'name'    => 'Melody Holiday Inn',
            'address' => [
                'city'     => 'taipei-city',
                'district' => 'da-an-district',
                'street'   => 'fuxing-south-road',
            ],
            'price'    => 31000.0, 
            'currency' => 'TWD',
        ];
        
        $mockStrategy = $this->createMock(CurrencyConverterStrategyInterface::class);
        $mockStrategy->expects(self::once())
            ->method('convert')
            ->with(1000.0)
            ->willReturn(31000.0);

        $mockFactory = $this->createMock(CurrencyStrategyFactory::class);
        $mockFactory->expects(self::once())
             ->method('create')
             ->willReturn($mockStrategy);

        $converter = new CurrencyConverter($mockFactory);   
        self::assertSame($expectedData, $converter->convert($data)->toArray());
    }
}