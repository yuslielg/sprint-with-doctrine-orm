<?php

namespace Contrask\Component\Sprint\Manager;

use Doctrine\ORM\EntityManager;
use Contrask\Component\Sprint\Model\Sprint;

/**
 * @author Yusliel Garcia <yuslielg@gmail.com>
 */
class SprintManager implements SprintManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * Constructor
     *
     * Additionally it creates a repository using $em, for given class
     *
     * @param EntityManager $em
     */
    public function __construct(
        EntityManager $em
    )
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Contrask\Component\Sprint\Model\Sprint');
    }

    /**
     * Picks a sprint using given criteria.
     *
     * @api
     * @param string|array $criteria
     * @return mixed
     */
    public function pick($criteria)
    {
        if (is_string($criteria)) {
           $criteria = array('strid' => $criteria);
        }

        return $this->repository->findOneBy($criteria);
    }

    /**
     * Collects the sprints by given criteria.
     * It returns all sprints if criteria is null.
     *
     * @api
     * @param mixed $criteria
     * @return array
     */
    public function collect($criteria = null)
    {
        if (null === $criteria) {
            return $this->repository->findAll();
        }

        return $this->repository->findBy($criteria);
    }

    /**
     * Adds given sprint
     *
     * @param Sprint $sprint
     * @return void
     */
    public function add(Sprint $sprint)
    {
        $this->em->persist($sprint);
        $this->em->flush();
    }

    /**
     * Updates given sprint
     *
     * @param Sprint $sprint
     * @return void
     */
    public function update(Sprint $sprint)
    {
        $this->em->flush($sprint);
    }

    /**
     * Removes given sprint
     *
     * @param Sprint $sprint
     * @return void
     */
    public function remove(Sprint $sprint)
    {
        $this->em->remove($sprint);
        $this->em->flush();
    }
}