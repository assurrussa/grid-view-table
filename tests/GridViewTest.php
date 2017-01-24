<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GridViewTest extends TestCase
{

    /**
     * @see          \Assurrussa\GridView\GridView::columns
     */
    public function testColumns()
    {
        /** @var \Assurrussa\GridView\GridView $gridView */
        $gridView = $this->app->make(\Assurrussa\GridView\GridView::NAME);
        $gridView->column()->setKey('id')->setValue('ID')->setSort(true);
        $gridView->column()->setKey('name')->setValue('name label')->setSort(true)->setHandler(function ($data) {
            return $data->name . ' ' . $data->name;
        });
        $gridView->columnActions(function ($data) use ($gridView) {
            /** @var \Assurrussa\AmiCMS\Models\Model $data */
            $buttons = [];
            $buttons[] = $gridView->columnAction()->setActionDelete();
            $buttons[] = $gridView->columnAction()->setActionEdit();
            return $buttons;
        });
        $gridView->button()->setButtonCreate();
        $gridView->button()->setButtonExport();
        $gridView->button()->setButtonCheckboxAction('create');
        $this->assertEquals(true, is_array($gridView->buttons->getButtons()));
        $this->assertEquals(3, count($gridView->buttons->getButtons()));
        $this->assertEquals(true, is_array($gridView->buttons->getButtonCreate(true)));
        $this->assertEquals(2, count($gridView->buttons->getButtons()));
        $this->assertEquals(true, is_array($gridView->buttons->getButtonExport(true)));
        $this->assertEquals(1, count($gridView->buttons->getButtons()));
    }
}
