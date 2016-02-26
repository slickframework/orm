<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orm;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use PHPUnit_Framework_Assert as Assert;

/**
 * Step definitions for slick/orm package
 *
 * @package Orm
 * @behatContext
 */
class OrmContext extends \AbstractContext implements
    Context, SnippetAcceptingContext
{

}