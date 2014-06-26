<?php

namespace Todo\Bundle\CoreBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Todo\Bundle\CoreBundle\Repository\ItemRepository")
 * @Serializer\AccessType("public_method")
 */
class Item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\AccessType("property")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Item name cannot be blank")
     */
    private $name;
    
    /**
     *
     * @var boolean
     * 
     * @ORM\Column(type="boolean")
     */
    private $isDone=false;

    /**
     * @var Todo\Bundle\CoreBundle\Entity\List
     *
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="items")
     * @Serializer\Type("Todo\Bundle\CoreBundle\Entity\TodoList")
     */
    private $todoList;
    
    /**
     *
     * @var integer
     * 
     * @ORM\Column(type="integer")
     */
    private $position;
    
    /**
     *
     * @var integer
     * @Serializer\Type("integer") 
     */
    private $todoListId;
    
    public function getTodoListId()
    {
        if($this->todoList)
        {
            return $this->todoList->getId();
        }
        return null;
    }
    
    public function setTodoListId($id)
    {
        $this->todoListId = $id;
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
     * @return Item
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
     * Set position
     *
     * @param integer $position
     * @return Item
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set todoList
     *
     * @param \Todo\Bundle\CoreBundle\Entity\TodoList $todoList
     * @return Item
     */
    public function setTodoList(\Todo\Bundle\CoreBundle\Entity\TodoList $todoList = null)
    {
        $this->todoList = $todoList;
        return $this;
    }

    /**
     * Get todoList
     *
     * @return \Todo\Bundle\CoreBundle\Entity\TodoList 
     */
    public function getTodoList()
    {
        return $this->todoList;
    }

    /**
     * Set isDone
     *
     * @param boolean $isDone
     * @return Item
     */
    public function setIsDone($isDone)
    {
        $this->isDone = $isDone;
    
        return $this;
    }

    /**
     * Get isDone
     *
     * @return boolean 
     */
    public function getIsDone()
    {
        return $this->isDone;
    }
}