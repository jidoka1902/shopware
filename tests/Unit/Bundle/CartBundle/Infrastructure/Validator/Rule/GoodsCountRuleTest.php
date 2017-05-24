<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Bundle\CartBundle\Infrastructure\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\CartBundle\Domain\Cart\CalculatedCart;
use Shopware\Bundle\CartBundle\Domain\LineItem\CalculatedLineItemCollection;
use Shopware\Bundle\CartBundle\Domain\LineItem\LineItem;
use Shopware\Bundle\CartBundle\Domain\Price\Price;
use Shopware\Bundle\CartBundle\Domain\Rule\Container\AndRule;
use Shopware\Bundle\CartBundle\Domain\Rule\Rule;
use Shopware\Bundle\CartBundle\Domain\Tax\CalculatedTaxCollection;
use Shopware\Bundle\CartBundle\Domain\Tax\TaxRuleCollection;
use Shopware\Bundle\CartBundle\Domain\Voucher\CalculatedVoucher;
use Shopware\Bundle\CartBundle\Domain\Voucher\VoucherProcessor;
use Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule;
use Shopware\Bundle\StoreFrontBundle\Common\StructCollection;
use Shopware\Bundle\StoreFrontBundle\Context\ShopContext;
use Shopware\Tests\Unit\Bundle\CartBundle\Common\DummyProduct;

class GoodsCountRuleTest extends TestCase
{
    public function testGteExactMatch(): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule::OPERATOR_GTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                    new DummyProduct('SW2'),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testGteWithVoucher(): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, GoodsCountRule::OPERATOR_GTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                    new DummyProduct('SW2'),
                    new CalculatedVoucher(
                        'Code1',
                        new LineItem(1, VoucherProcessor::TYPE_VOUCHER, 1),
                        new Price(-1, -1, new CalculatedTaxCollection(), new TaxRuleCollection()),
                        new AndRule()
                    ),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testGteNotMatch(): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule::OPERATOR_GTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testLteExactMatch(): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, GoodsCountRule::OPERATOR_LTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                    new DummyProduct('SW2'),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testLteWithVoucher(): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule::OPERATOR_LTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                    new DummyProduct('SW2'),
                    new CalculatedVoucher(
                        'Code1',
                        new LineItem(1, VoucherProcessor::TYPE_VOUCHER, 1),
                        new Price(-1, -1, new CalculatedTaxCollection(), new TaxRuleCollection()),
                        new AndRule()
                    ),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertTrue(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function testLteNotMatch(): void
    {
        $rule = new GoodsCountRule(2, GoodsCountRule::OPERATOR_LTE);

        $cart = $this->createMock(CalculatedCart::class);

        $cart->expects($this->any())
            ->method('getCalculatedLineItems')
            ->will($this->returnValue(
                new CalculatedLineItemCollection([
                    new DummyProduct('SW1'),
                    new DummyProduct('SW2'),
                    new DummyProduct('SW3'),
                ])
            ));

        $context = $this->createMock(ShopContext::class);

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    /**
     * @dataProvider unsupportedOperators
     *
     * @expectedException \Shopware\Bundle\CartBundle\Domain\Rule\Exception\UnsupportedOperatorException
     *
     * @param string $operator
     */
    public function testUnsupportedOperators(string $operator): void
    {
        $rule = new \Shopware\Bundle\CartBundle\Infrastructure\Rule\GoodsCountRule(2, $operator);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $this->assertFalse(
            $rule->match($cart, $context, new StructCollection())->matches()
        );
    }

    public function unsupportedOperators(): array
    {
        return [
            [true],
            [false],
            [''],
            [Rule::OPERATOR_EQ],
            [\Shopware\Bundle\CartBundle\Domain\Rule\Rule::OPERATOR_NEQ],
        ];
    }
}
