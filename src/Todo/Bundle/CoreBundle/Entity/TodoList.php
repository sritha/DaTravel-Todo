<?php

namespace Todo\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * TodoList
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Todo\Bundle\CoreBundle\Repository\TodoListRepository")
 * @Serializer\AccessType("public_method")
 */
class TodoList
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\ReadOnly
     * 
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\OneToMany(targetEntity="Item", mappedBy="todoList")
     * @Serializer\ReadOnly
     */
    private $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TodoList
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add iems
     *
     * @param \Todo\Bundle\CoreBundle\Entity\List $iems
     * @return TodoList
     */
    public function addItem(\Todo\Bundle\CoreBundle\Entity\Item $items)
    {
        $this->items[] = $items;
    
        return $this;
    }

    /**
     * Remove iems
     *
     * @param \Todo\Bundle\CoreBundle\Entity\List $items
     */
    public function removeItem(\Todo\Bundle\CoreBundle\Entity\TodoList $item)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
}