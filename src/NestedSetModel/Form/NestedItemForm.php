<?php
namespace NestedSetModel\Form;

use Zend\Form\Form;

class NestedItemForm extends Form 
{
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden'
        ));
        
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name'
            )
        )); 
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton'
            )
        ));
    }
}