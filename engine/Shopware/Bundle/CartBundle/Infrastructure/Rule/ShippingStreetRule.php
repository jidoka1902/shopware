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

namespace Shopware\Bundle\CartBundle\Infrastructure\Rule;

use Shopware\Bundle\CartBundle\Domain\Cart\CalculatedCart;
use Shopware\Bundle\CartBundle\Domain\Rule\Match;
use Shopware\Bundle\CartBundle\Domain\Rule\Rule;
use Shopware\Bundle\StoreFrontBundle\Common\StructCollection;
use Shopware\Bundle\StoreFrontBundle\Context\ShopContextInterface;

class ShippingStreetRule extends Rule
{
    /**
     * @var string
     */
    protected $streetName;

    /**
     * @param string $streetName
     */
    public function __construct($streetName)
    {
        $this->streetName = $streetName;
    }

    public function match(
        CalculatedCart $calculatedCart,
        ShopContextInterface $context,
        StructCollection $collection
    ): Match {
        if (!$location = $context->getShippingLocation()->getAddress()) {
            return new Match(
                false,
                ['Shipping location has no address']
            );
        }

        $value = strtolower($this->streetName);

        return new Match(
            (bool) preg_match("/$value/", strtolower($location->getStreet())),
            ['Shipping street not matched']
        );
    }
}
