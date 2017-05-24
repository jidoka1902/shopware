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

namespace Shopware\Tests\Unit\Bundle\CartBundle\Domain\Error;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\CartBundle\Domain\Error\ErrorCollection;

class ErrorCollectionTest extends TestCase
{
    public function testHasError(): void
    {
        $collection = new ErrorCollection([
            new OtherError(),
            new Error(1),
        ]);

        $this->assertTrue($collection->has(OtherError::class));
        $this->assertTrue($collection->has(Error::class));

        $collection->clear();

        $this->assertFalse($collection->has(OtherError::class));
        $this->assertFalse($collection->has(Error::class));
    }

    public function testHasErrorLevel(): void
    {
        $collection = new ErrorCollection([
            new Error(1),
            new Error(2),
        ]);

        $this->assertTrue($collection->hasLevel(1));
        $this->assertTrue($collection->hasLevel(2));
        $this->assertFalse($collection->hasLevel(3));
    }
}

class OtherError extends \Shopware\Bundle\CartBundle\Domain\Error\Error
{
    public function getMessageKey(): string
    {
        return '';
    }

    public function getMessage(): string
    {
        return '';
    }

    public function getLevel(): int
    {
        return 1;
    }
}

class Error extends \Shopware\Bundle\CartBundle\Domain\Error\Error
{
    /**
     * @var int
     */
    private $level;

    /**
     * @param int $level
     */
    public function __construct($level)
    {
        $this->level = $level;
    }

    public function getMessageKey(): string
    {
        return '';
    }

    public function getMessage(): string
    {
        return '';
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
