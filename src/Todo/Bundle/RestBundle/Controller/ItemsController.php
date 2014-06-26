<?php

namespace Todo\Bundle\RestBundle\Controller;

use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use Todo\Bundle\CoreBundle\Entity as Entities;
use JMS\Serializer\SerializationContext;
/**
 * @Route("/items")
 */
class ItemsController extends FOSRestController
{
    /**
     * @return \FOS\RestBundle\View\View
     * @Route("/")
     */
    public function getItemsAction()
    {
        $view = View::create();
        //$view->setFormat('json');
        $todoList = $this->getDoctrine()->getRepository('TodoCoreBundle:Item')->find(1);
        $data = array($todoList);
        $view->setData($data);
        $view->setTemplateVar('items');
        return $view;
    }
}