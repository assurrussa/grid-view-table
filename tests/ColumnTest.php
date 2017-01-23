<?php

use Assurrussa\GridView\Support\Column;

class ColumnTest extends TestCase
{

    /**
     *
     */
    public function testColumn()
    {
        $column = new Column();
        $this->assertTrue(true, $column instanceof Assurrussa\GridView\Interfaces\ColumnInterface);
        $this->assertEquals('', $column->getKey());
        $this->assertEquals('', $column->getValue());
        $this->assertEquals(false, $column->isSort());
        $this->assertEquals(false, $column->isScreening());
        $this->assertEquals([], $column->getFilter());
        $this->assertEquals(false, $column->isHandler());
        $this->assertEquals(null, $column->getHandler());


        $column->setKey('test')
            ->setValue('name column')
            ->setSort(false)
            ->setScreening(true)
            ->setHandler(function ($data) {
                return $data == null;
            })
            ->setFilter('field', ['key' => 'value']);
        $this->assertEquals('test', $column->getKey());
        $this->assertEquals('name column', $column->getValue());
        $this->assertEquals(false, $column->isSort());
        $this->assertEquals(true, $column->isScreening());
        $this->assertEquals([
            'name' => 'field',
            'data' => ['key' => 'value'],
            'mode' => '',
        ], $column->getFilter());
        $this->assertEquals(true, $column->isHandler());
        $this->assertEquals(null, $column->getHandler());
    }

    /**
     *
     */
    public function testColumnFilter()
    {
        $column = new Column();
        $column->setKey('test')
            ->setValue('name column')
            ->setFilter('field', []);
        $this->assertEquals(['name' => 'field', 'data' => [], 'mode' => ''], $column->getFilter());
        $this->assertEquals(false, $column->getDateActive());
        $this->assertEquals(Column::DEFAULT_TO_STRING_FORMAT, $column->getDateFormat());

        $column->setFilter('field1', 'text');
        $this->assertEquals(['name' => 'field1', 'data' => 'text', 'mode' => ''], $column->getFilter());

        $column->setFilterString('field2', 'text');
        $this->assertEquals(['name' => 'field2', 'data' => 'text', 'mode' => 'string'], $column->getFilter());

        $column->setFilterDate('field2', 'date', true, 'Y-m-d');
        $this->assertEquals(['name' => 'field2', 'data' => 'date', 'mode' => 'date'], $column->getFilter());
        $this->assertEquals(true, $column->getDateActive());
        $this->assertEquals('Y-m-d', $column->getDateFormat());
    }

    /**
     *
     */
    public function testColumnActions()
    {
        $column = new Column();
        $this->assertEquals(false, $column->isKeyAction());
        $this->assertEquals(false, $column->getActions() instanceof Closure);
        $column->setKey('test')
            ->setValue('name column')
            ->setActions(function ($data) {
                return $data == null;
            });
        $this->assertEquals(Column::ACTION_NAME, $column->getKey());
        $this->assertEquals(Column::ACTION_NAME, $column->getValue());
        $this->assertEquals(true, $column->isKeyAction());
        $this->assertEquals(true, $column->getActions() instanceof Closure);
    }

    /**
     *
     */
    public function testGetColumnValues()
    {
        $column = new Column();
        $column->setKey('key')
            ->setValue('name column')
            ->setActions(function ($data) {
                return $data->action == 1;
            });
        $object = new stdClass();
        $object->action = 1;
        $this->assertEquals(true, $column->isKeyAction());
        $this->assertEquals(true, $column->getValues($object));

        $column = new Column();
        $column->setKey('key')
            ->setValue('name column')
            ->setHandler(function ($data) {
                return $data->key == 1;
            });
        $object = new stdClass();
        $object->key = 1;
        $this->assertEquals(false, $column->isKeyAction());
        $this->assertEquals(true, $column->getValues($object));
        $object->key = 2;
        $this->assertEquals(false, $column->getValues($object));
    }

    /**
     *
     */
    public function testColumnInstance()
    {
        $column = new Column();
        $column->setKey('key');

        $object = new stdClass();
        $object->key = 2;

        $this->assertEquals(null, $column->getValues());
        $this->assertEquals(2, $column->getValues($object));

        $column = new Column();
        $column->setKey('key');
        $this->assertEquals(null, $column->getValues());

        $column->setInstance($object);
        $this->assertEquals(2, $column->getValues());
    }
}
