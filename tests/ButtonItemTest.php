<?php

use Assurrussa\GridView\Support\Button;

class ButtonItemTest extends TestCase
{
    /**
     *
     */
    public function testButtonItem()
    {
        $button = new Button();
        $this->assertTrue(true, $button instanceof Assurrussa\GridView\Interfaces\ButtonInterface);

        $button->setAction('show')
            ->setLabel('show')
            ->setRoute('show', ['entity', 1])
            ->setIcon('icon')
            ->setHandler(function ($data) {
                return $data == null;
            });

        $this->assertEquals('show', $button->getAction());
        $this->assertEquals('show', $button->getLabel());
        $this->assertEquals('icon', $button->getIcon());
        $this->assertEquals(true, $button->isVisibility());
        $this->assertEquals(true, strpos($button->getUrl(), '://'));
        $button->setUrl('#');
        $this->assertEquals('#', $button->getUrl());
        $button->setVisible(false);
        $this->assertEquals(false, $button->isVisibility());

        $button = new Button();
        $button->setVisible(false);
        $button->setAction('show')
            ->setLabel('show')
            ->setRoute('show', ['entity', 1])
            ->setIcon('icon')
            ->setHandler(function ($data) {
                return $data == null;
            });

        $this->assertEquals(null, $button->getAction());
        $this->assertEquals(null, $button->getLabel());
        $this->assertEquals(null, $button->getIcon());
        $this->assertEquals(false, $button->isVisibility());
        $this->assertEquals(false, strpos($button->getUrl(), '://'));
        $button->setUrl('#');
        $this->assertEquals('', $button->getUrl());
        $button->setVisible(false);
        $this->assertEquals(false, $button->isVisibility());
    }

    /**
     *
     */
    public function testButtonItemEdit()
    {
        $button = new Button();
        $button->setActionEdit();
        $this->assertEquals('<a href="#"
   class=" btn btn-default btn-sm flat"
   id=""
   data-toggle="tooltip"
   title="Edit"
>
        <i class="fa fa-pencil"></i>
        </a>
', (string)$button);
        $button = new Button();
        $button->setActionEdit()
            ->setTitle('title')
            ->setLabel('label')
            ->setClass('test-class')
            ->setJsClass('test-js-class')
            ->setId('test-id')
            ->setConfirmText('confirm-text')
            ->setHandler(function ($data) {
                return $data == null;
            });
        $this->assertEquals('<a href="#"
   class="test-js-class test-class"
   id="test-id"
   data-toggle="tooltip"
   title="title"
>
        <i class="fa fa-pencil"></i>
        label</a>
', (string)$button);

    }
}
