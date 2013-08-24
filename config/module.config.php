<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'NestedSetModel\Controller\Category' => 'NestedSetModel\Controller\CategoryController'
        )
    ),
    
    'router' => array(
        'routes' => array(
            'nested_category' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/category[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'NestedSetModel\Controller\Category',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'nested_category' => __DIR__ .'/../view/'
        )
    )
);