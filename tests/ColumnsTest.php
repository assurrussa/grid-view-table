<?php

use Assurrussa\GridView\Support\Columns;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ColumnsTest extends TestCase
{

    /**
     *
     */
    public function testColumns()
    {
        $columns = new Columns();
        $this->assertTrue($columns instanceof Assurrussa\GridView\Interfaces\ColumnsInterface);

        $this->assertEquals(0, $columns->count());
        $this->assertEquals(0, count($columns->getActions()));
        $this->assertEquals(0, count($columns->getColumns()));
        $this->assertTrue(is_array($columns->toArray()));

        $columns->setColumn(new \Assurrussa\GridView\Support\Column());
        $columns->setColumn(new \Assurrussa\GridView\Support\Column());
        $column = (new \Assurrussa\GridView\Support\Column());
        $columns->setColumn(
            $column->setActions(function ($data) {
                $this->assertTrue($data instanceof \Assurrussa\GridView\Models\Model);
                $this->assertEquals(2, $data->action);
                return [
                    (new \Assurrussa\GridView\Support\Button())->setActionEdit(),
                    (new \Assurrussa\GridView\Support\Button())->setActionDelete(),
                    (new \Assurrussa\GridView\Support\Button())->setActionRestore(),
                    (new \Assurrussa\GridView\Support\Button())->setActionShow(),
                ];
            })
        );
        $object = new \Assurrussa\GridView\Models\Model();
        $object->action = 2;
        $column->setInstance($object);

        $this->assertEquals(2, $column->getValues());
        $this->assertEquals(3, $columns->count());
        $this->assertEquals(4, count($columns->getActions()));
        $this->assertEquals(3, count($columns->getColumns()));
        $this->assertTrue(is_array($columns->toArray()));
    }
}
