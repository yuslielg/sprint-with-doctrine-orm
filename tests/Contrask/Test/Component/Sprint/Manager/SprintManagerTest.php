<?php

namespace Contrask\Test\Component\Sprint\Manager;

use Contrask\Component\Sprint\Model\Sprint;
use Contrask\Component\Sprint\Manager\SprintManager;
use Contrask\Test\Component\Sprint\EntityManagerBuilder;
use Doctrine\ORM\EntityManager;

/**
 * @author Yusliel Garcia <yuslielg@gmail.com>
 */
class SprintManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    public function setUp()
    {
        $builder = new EntityManagerBuilder();
        $this->em = $builder->createEntityManager(
            array(
                realpath(sprintf("%s/../../../../../../src/Contrask/Component/Sprint/Resources/config/doctrine", __DIR__)),
            ),
            array(
                'Contrask\Component\Sprint\Model\Sprint',
            )
        );
    }

    /**
     * @covers \Contrask\Component\Project\Manager\ProjectManager::__construct
     */
    public function testConstructor()
    {
        $manager = new SprintManager($this->em);

        $this->assertAttributeEquals($this->em, 'em', $manager);
        $this->assertAttributeEquals($this->em->getRepository('Contrask\Component\Sprint\Model\Sprint'), 'repository', $manager);
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::pick
     */
    public function testPickWithStringCriteria()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');
        $this->em->persist($sprint);

        $this->em->flush();

        /*Tests*/
        $sprintManager = new SprintManager($this->em);
        $this->assertEquals($sprint, $sprintManager->pick('foo'));
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::pick
     */
    public function testPickWithArrayCriteria()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');
        $this->em->persist($sprint);

        $this->em->flush();

        /*Tests*/
        $sprintManager = new ProjectManager($this->em);
        $this->assertEquals($sprint, $sprintManager->pick(array('name' => 'bar')));
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::collect
     */
    public function testCollectWithNullCriteria()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');
        $this->em->persist($sprint);

        $sprint = new Sprint();
        $sprint->setStrid('foo 1');
        $sprint->setName('bar 1');
        $this->em->persist($sprint);

        $this->em->flush();

        /*Tests*/
        $sprintManager = new ProjectManager($this->em);
        $this->assertEquals(2, count($sprintManager->collect()));
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::collect
     */
    public function testCollectWithArrayCriteria()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');
        $this->em->persist($sprint);

        $sprint = new Sprint();
        $sprint->setStrid('foo 1');
        $sprint->setName('bar 1');
        $this->em->persist($sprint);

        $this->em->flush();

        /*Tests*/
        $sprintManager = new ProjectManager($this->em);
        $this->assertEquals(1, count($sprintManager->collect(array('strid' => 'foo'))));
        $this->assertEquals(0, count($sprintManager->collect(array('name' => 'foo'))));
    }

    /**
    * @covers \Contrask\Component\Sprint\Manager\SprintManager::add
    */
    public function testAdd()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');

        /*Tests*/
        $sprintManager = new SprintManager($this->em);
        $sprintManager->add($sprint);
        $this->assertEquals(1, count($sprintManager->collect()));
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::update
     */
    public function testUpdate()
    {
        /*Fixtures*/
        $sprint = new Sprint();
        $sprint->setStrid('foo');
        $sprint->setName('bar');
        $this->em->persist($sprint);
        $this->em->flush();

        /*Tests*/
        $sprintManager = new SprintManager($this->em);
        $sprint = $sprintManager->pick('foo');
        $sprint->setName('bar 1');
        $sprintManager->update($sprint);
        $this->assertEquals('bar 1', $sprintManager->pick('foo')->getName());
    }

    /**
     * @covers \Contrask\Component\Sprint\Manager\SprintManager::remove
     */
    public function testRemove()
    {
        /*Fixtures*/
        $sprint1 = new Sprint();
        $sprint1->setStrid('foo');
        $sprint1->setName('bar');
        $this->em->persist($sprint1);

        $sprint2 = new Sprint();
        $sprint2->setStrid('foo 1');
        $sprint2->setName('bar 1');
        $this->em->persist($sprint2);

        /*Tests*/
        $sprintManager = new SprintManager($this->em);
        $sprintManager->remove($sprint1);
        $this->assertEquals(1, count($sprintManager->collect()));
    }
}