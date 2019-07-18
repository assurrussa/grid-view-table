## Export Data

##### Example settings export data for query result
```
    $gridView = app(\Assurrussa\GridView\GridView::NAME)->setQuery($query);
    $gridView->button()->setButtonExport($url);
    $gridView->setExport(true);
    $gridView->setFieldsForExport([
       'ID'   => 'id',
       'Name' => 'name',
    ]);
    if($resultExport = $gridView->get()->getExport()) {
        return $resultExport;
    }
```
