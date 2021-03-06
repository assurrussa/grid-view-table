<?php

use Assurrussa\GridView\Support\Column;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
            'name'        => 'field',
            'data'        => ['key' => 'value'],
            'mode'        => 'select',
            'class'       => '',
            'style'       => '',
            'selected'    => [],
            'placeholder' => '',
            'width'       => '180px',
            'format'      => 'DD MMM YY',
        ], $column->getFilter());
        $this->assertEquals(true, $column->isHandler());
        $this->assertEquals(true, $column->getHandler() instanceof Closure);
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
        $this->assertEquals([
            'name'        => 'field',
            'data'        => [],
            'mode'        => 'select',
            'class'       => '',
            'style'       => '',
            'selected'    => [],
            'placeholder' => '',
            'width'       => '180px',
            'format'      => 'DD MMM YY',
        ], $column->getFilter());
        $this->assertEquals(false, $column->getDateActive());
        $this->assertEquals(Column::DEFAULT_TO_STRING_FORMAT, $column->getDateFormat());

        $column->setFilter('field1', 'text');
        $this->assertEquals([
            'name'        => 'field1',
            'data'        => 'text',
            'mode'        => 'select',
            'class'       => '',
            'style'       => '',
            'selected'    => [],
            'placeholder' => '',
            'width'       => '180px',
            'format'      => 'DD MMM YY',
        ], $column->getFilter());

        $column->setFilterString('field2');
        $this->assertEquals([
            'name'        => 'field2',
            'data'        => '',
            'mode'        => 'string',
            'class'       => '',
            'style'       => '',
            'selected'    => [],
            'placeholder' => '',
            'width'       => '180px',
            'format'      => 'DD MMM YY',
        ], $column->getFilter());

        $column->setFilterDate('field2', 'date', true, 'Y-m-d');
        $this->assertEquals([
            'name'        => 'field2',
            'data'        => 'date',
            'mode'        => 'date',
            'class'       => '',
            'style'       => '',
            'selected'    => [],
            'placeholder' => '',
            'width'       => '180px',
            'format'      => 'DD MMM YY',
        ], $column->getFilter());
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
        $this->assertEquals([], $column->getActions());
        $column->setKey('test')
            ->setValue('name column')
            ->setActions(function ($data, $columns) {
                /**
                 * @var \App\Post                           $data
                 * @var \Assurrussa\GridView\Support\Column $columns
                 */
                $columns->addButton()
                    ->setActionShow()
                    ->setUrl('http://test.com')
                    ->setHandler(function ($data) {
                        return $data->id == 3;
                    });
                $columns->addButton()
                    ->setActionShow()
                    ->setUrl('http://test.com')
                    ->setHandler(function ($data) {
                        return false;
                    });
            });
        $object = new \Assurrussa\GridView\Models\Model();
        $object->id = 3;
        $this->assertEquals(Column::ACTION_NAME, $column->getKey());
        $this->assertEquals('', $column->getValue());
        $this->assertEquals(true, $column->isKeyAction());
        $this->assertEquals([], $column->getActions());
        $column->setInstance($object);
        $this->assertEquals(2, count($column->getActions()));
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
        $object = new \Assurrussa\GridView\Models\Model();
        $object->action = 1;
        $this->assertEquals(true, $column->isKeyAction());
        $this->assertEquals(true, $column->getValues($object));

        $column = new Column();
        $column->setKey('key')
            ->setValue('name column')
            ->setHandler(function ($data) {
                return $data->key == 1;
            });
        $object = new \Assurrussa\GridView\Models\Model();
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

        $object = new \Assurrussa\GridView\Models\Model();
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
