<?php

namespace NestedSetModel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use NestedSetModel\Model\Category;
use NestedSetModel\Form\NestedItemForm;

class CategoryController extends AbstractActionController {

    protected $categoryTable;

    public function indexAction() {
        return new ViewModel(array(
            'categories' => $this->getDbTable()->fetchAll()
        ));
    }

    /**
     * Add new category
     * 
     * Depends on the presented or missing "dir" parameter, there are several 
     * possible actions:
     * 1. Missing dir parameter - add a top level category. If there are already 
     * some categories, we must update these to be sub-categories.
     * 2. Presented "dir" parameter with value equals "same" - add a new category 
     * of the same level as the passed id value. 
     * 3. Presented "dir" parameter with value of "sub" - add a new sub category 
     * of the selected category id.
     * 
     * In addition, if the "dir" parameter is presented, an id of existing category 
     * must be provided as well.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction() {

        $id = $this->params()->fromRoute('id');

        if (!$id) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'index'));
        }

        try {
            $ref_category = $this->getDbTable()->getCategory($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'index'));
        }


        $form = new NestedItemForm('category');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $category = new Category();

            $form->setInputFilter($category->getInputFilter());

            $categoryData = $request->getPost();


            $form->setData($categoryData);

            if ($form->isValid()) {
                $category->exchangeArray($form->getData());
                $this->getDbTable()->saveCategory($category, $id);
                return $this->redirect()->toRoute('nested_category');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'ref_category' => $ref_category
        ));
    }

    public function editAction() {

        $id = $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'add'));
        }

        try {
            $category = $this->getDbTable()->getCategory($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'index'));
        }

        $form = new NestedItemForm('category');
        $form->bind($category);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDbTable()->saveCategory($category);

                return $this->redirect()->toRoute('nested_category');
            }
        }

        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'add'));
        }

        try {
            $category = $this->getDbTable()->getCategory($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('nested_category', array('action' => 'index'));
        }


        $request = $this->getRequest();

        if ($request->isPost()) {

            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {

                $id = (int) $request->getPost('id');
                $this->getDbTable()->deleteCategory($id);

                return $this->redirect()->toRoute('nested_category');
            }
        }

        return array(
            'category' => $category
        );
    }

    public function getDbTable() {
        if (!$this->categoryTable) {
            $sm = $this->getServiceLocator();
            $this->categoryTable = $sm->get('NestedSetModel\Model\CategoryTable');
        }

        return $this->categoryTable;
    }

}

