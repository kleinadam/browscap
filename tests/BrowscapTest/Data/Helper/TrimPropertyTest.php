<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\TrimProperty;

/**
 * Class ExpanderTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class TrimPropertyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\TrimProperty
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new TrimProperty();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testTrue() : void
    {
        self::assertSame(true, $this->object->trimProperty('true'));
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testFalse() : void
    {
        self::assertSame(false, $this->object->trimProperty('false'));
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDefault() : void
    {
        self::assertSame('abc', $this->object->trimProperty('  abc '));
    }
}
