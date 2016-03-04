<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Database\Sql\Select;
use Slick\Orm\RepositoryInterface;

/**
 * QueryObject
 *
 * @package Slick\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class QueryObject extends Select implements QueryObjectInterface
{

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * QueryObject has a repository as a dependency.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->adapter = $repository->getAdapter();
        parent::__construct(
            $repository->getEntityDescriptor()->getTableName()
        );
    }

    /**
     * Retrieve all records matching this select query
     *
     * @return \Slick\Database\RecordList
     */
    public function all()
    {
        $data = $this->adapter->query($this, $this->getParameters());
        return $this->repository->getEntityMapper()->createFrom($data);
    }

    /**
     * Retrieve first record matching this select query
     *
     * @return mixed
     */
    public function first()
    {
        $sql = clone($this);
        $sql->limit(1);
        $result = $this->adapter->query($sql, $sql->getParameters());
        return  (count($result) > 0) ? $result[0] : null;
    }
}