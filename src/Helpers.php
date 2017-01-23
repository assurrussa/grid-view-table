<?php

if(function_exists('amiGrid')) {
    /**
     * @return \Assurrussa\GridView\GridView
     */
    function amiGrid()
    {
        return app(\Assurrussa\GridView\GridView::NAME);
    }
}