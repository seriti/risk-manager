<?php
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/setup_app.php file within this framework
add the below code snippet to the end of existing "src/setup_app.php" file.
This tells the framework about module: name, sub-memnu route list and title, database table prefix.
*/

$container['config']->set('module','risk',['name'=>'Risk manager',
                                            'route_root'=>'admin/risk/',
                                            'route_list'=>['dashboard'=>'Dashboard','objective'=>'Objectives','risk'=>'Risks','control'=>'Controls',
                                                           'indicator'=>'Indicators','action'=>'Actions','task'=>'Tasks','report'=>'Reports'],
                                            'table_prefix'=>'rsk_'
                                            ]);


