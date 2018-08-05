<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GridViewTest extends TestCase
{

    /**
     * @see          \Assurrussa\GridView\GridView::columns
     */
    public function testColumns()
    {
        /** @var \Assurrussa\GridView\GridView $gridView */
        $gridView = $this->app->make(\Assurrussa\GridView\GridView::NAME);
        $gridView->column('id', 'ID')->setSort(true);
        $gridView->column('name', 'name label')->setSort(true)->setHandler(function ($data) {
            return $data->name . ' ' . $data->name;
        });
        $gridView->columnActions(function ($data) use ($gridView) {
            /** @var \Assurrussa\GridView\Models\Model $data */
            $buttons = [];
            $buttons[] = $gridView->columnAction()->setActionDelete();
            $buttons[] = $gridView->columnAction()->setActionEdit();
            return $buttons;
        });
        $gridView->button()->setButtonCreate('http://test.com');
        $gridView->button()->setButtonExport();
        $gridView->button()->setButtonCheckboxAction('create');
        $this->assertEquals(true, is_array($gridView->buttons->getButtons()));
        $this->assertEquals(3, count($gridView->buttons->getButtons()));
        $this->assertEquals(true, is_array($gridView->buttons->getButtonCreateToArray()));
        $this->assertEquals(2, count($gridView->buttons->getButtons()));
        $this->assertEquals(true, is_array($gridView->buttons->getButtonExportToArray()));
        $this->assertEquals(1, count($gridView->buttons->getButtons()));
    }
}
