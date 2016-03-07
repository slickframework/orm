<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Repository;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Entity\CollectionsMapInterface;
use Slick\Orm\Repository\AbstractRepository;
use Slick\Orm\Repository\IdentityMapInterface;

/**
 * AbstractRepository test case
 *
 * @package Slick\Tests\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AbstractRepositoryTest extends TestCase
{

    /**
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * Sets the SUT abstract repository object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->getMockForAbstractClass(
            AbstractRepository::class
        );
    }

    /**
     * Should create a new IdentityMap if none is set before
     * @test
     */
    public function getIdentityMap()
    {
        $idMap = $this->repository->getIdentityMap();
        $this->assertInstanceOf(IdentityMapInterface::class, $idMap);
    }

    /**
     * Should create a new collection map in none is set
     * @test
     */
    public function getCollectionsMap()
    {
        $colMap = $this->repository->getCollectionsMap();
        $this->assertInstanceOf(CollectionsMapInterface::class, $colMap);
    }
}
