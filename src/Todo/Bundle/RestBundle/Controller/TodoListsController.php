<?php

namespace Todo\Bundle\RestBundle\Controller;

use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use Todo\Bundle\CoreBundle\Entity as Entities;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/todoLists")
 */
class TodoListsController extends RestController
{
    
    /**
     * Adds a new Todo list
     * @return \FOS\RestBundle\View\View
     * @Route("/")
     * @Method({"POST"})
     */
    public function createTodoListAction()
    {
        $view = View::create();
        $serializer = $this->getSerializer();
        $todoList = $serializer->deserialize($this->getRequest()->getContent(),
                'Todo\Bundle\CoreBundle\Entity\TodoList',
                'json');
       // var_dump($this->getRequest()->getContent());
        $em = $this->getEm();
        $em->persist($todoList);
        $em->flush();
        $view->setStatusCode(201);
        $view->setData($todoList);
        return $view;
    }
    /**
     * Retrieves all the Todo lists
     * @return \FOS\RestBundle\View\View
     * @Route("/")
     * @Method({"GET"})
     */
    public function getTodoListsAction()
    {
        $view = View::create();
        $todoLists = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->findAll();
        $view->setData($todoLists);
        return $view;
    }

    /**
     * Retrieves Todo list based on the id
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @Route("/{id}")
     * @Method({"GET"})
     */
    public function getTodoListAction($id)
    {
        $view = View::create();
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->find($id);
        if(!$todoList)
        {
            return $this->jsonErrorResponse(
                    "The todo list with an id of $id does not exist",
                    "Todo List not found",
                    404);
        }
        $view->setData($todoList);
        return $view;
    }
      
    /**
     * Updates Todo list based on id
     * @param $id
     * @return void
     * @Route("/{id}")
     * @Method({"PUT"})
     */    
    public function updateTodoListAction($id)
    {
        $view = View::create();
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->find($id);
        if(!$todoList)
        {
            return $this->jsonErrorResponse(
                    "The todo list with an id of $id does not exist",
                    "Todo List not found",
                    404);
        }
        $view->setData($todoList);       
    }
    
    /**
     * Deletes the Todo list based on the id
     * @Route("/{id}")
     * @Method({"DELETE"})
     */    
    public function deleteTodoListAction($id)
    {
        $view = View::create();
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->find($id);
        if(!$todoList)
        {
            return $this->jsonErrorResponse(
                "The todo list with an id of $id does not exist",
                "Todo List not found",
                404);
        }
        $em = $this->getEm();
        $em->remove($todoList);
        $em->flush();
        $view->setData($todoList);
    }

    /**
     * Create an item
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @Route("/{id}/items")
     * @Method({"POST"})
     */
    public function createItemAction($id)
    {
        $view = View::create();
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->find($id);
        if(!$todoList)
        {
            return $this->jsonErrorResponse(
                    "The todo list with an id of $id does not exist",
                    "Todo List not found",
                    404
            );
        }
        $item = $this->getSerializer()->deserialize(
                $this->getRequest()->getContent(),
                'Todo\Bundle\CoreBundle\Entity\Item',
                'json'
        );
        $item->setTodoList($todoList);
        $item->setPosition(0);
        $errors = $this->container->get('validator')->validate($item);
        if(isset($errors[0]))
        {
            return $this->jsonErrorResponse(
                $errors[0]->getMessage(),
                $errors[0]->getMessage(),
                400
            );
        }
        else
        {
            $this->getEm()->persist($item);
            $this->getEm()->flush();
            $view->setStatusCode(201);
            $view->setData($item);
        }
        return $view;
    }

    /**
     * Updates the item
     * @param $id
     * @param $itemId
     * @return \FOS\RestBundle\View\View
     * @Route("/{id}/items/{itemId}")
     * @Method({"PUT"})
     */
    public function updateItemAction($id, $itemId)
    {
        $view = View::create();
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:TodoList')->find($id);
        if(!$todoList)
        {
            return $this->jsonErrorResponse(
                    "The todo list with an id of $id does not exist",
                    "Todo List not found",
                    404
            );
        }
        $item = $this->getSerializer()->deserialize(
                $this->getRequest()->getContent(),
                'Todo\Bundle\CoreBundle\Entity\Item',
                'json'
        );
        $item = $this->getEm()->merge($item);
        $item->setTodoList($todoList);
        $this->getEm()->persist($item);
        $this->getEm()->flush();
        $view->setStatusCode(200);
        $view->setData($item);
        return $view;
    }
}