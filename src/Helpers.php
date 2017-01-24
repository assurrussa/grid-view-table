<?php
if(!function_exists('amiGrid')) {
    /**
     * @return \Assurrussa\GridView\GridView
     */
    function amiGrid()
    {
        return app(\Assurrussa\GridView\GridView::NAME);
    }
}
if(!function_exists('amiGridColumnCeil')) {
    /**
     * @return \Assurrussa\GridView\Support\ColumnCeil
     */
    function amiGridColumnCeil()
    {
        return \Assurrussa\GridView\GridView::columnCeil();
    }
}