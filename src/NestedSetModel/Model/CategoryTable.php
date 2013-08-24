<?php

namespace NestedSetModel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class CategoryTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function(Select $select) {
                    $select->order('lft');
                });
        return $resultSet;
    }

    public function getCategory($id) {
        $id = (int) $id;

        $rowset = $this->tableGateway->select(array('id' => $id));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveCategory(Category $category, $ref_category_id = 0) {
        $data = array(
            'name' => $category->name
        );

        $id = (int) $category->id;

        if ($id == 0) {

            $this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();

            try {

                if ($ref_category_id > 0) {

                    $ref_category = $this->getCategory($ref_category_id);

                    $this->tableGateway->getAdapter()->getDriver()->getConnection()->execute(
                            "UPDATE category SET 
                            lft = CASE WHEN lft > " . $ref_category->rgt . " THEN lft + 2 ELSE lft END,
                            rgt = CASE WHEN rgt >= " . $ref_category->rgt . " THEN rgt + 2 ELSE rgt END
                         WHERE rgt >= " . $ref_category->rgt
                    );

                    $data['lft'] = $ref_category->rgt;
                    $data['rgt'] = $ref_category->rgt + 1;
                } else {
                    $data['lft'] = 1;
                    $data['rgt'] = 2;
                }

                $this->tableGateway->insert($data);

                $this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
            } catch (\Exception $e) {
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
            }
        } else {
            if ($this->getCategory($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception("Category $id does not exists!");
            }
        }
    }

    public function deleteCategory($id) {

        $category = $this->getCategory($id);

        if ($category->id > 0) {

            $this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();

            try {

                $this->tableGateway->delete(array('id' => $id));

                if (($category->rgt - $category->lft) > 1) {
                    $this->tableGateway->getAdapter()->getDriver()->getConnection()->execute("UPDATE category SET lft = lft - 1, rgt = rgt - 1 WHERE lft BETWEEN ".$category->lft." AND ".$category->rgt);
//                    $this->tableGateway->update(array(
//                        "lft" => "lft - 1",
//                        "rgt" => "rgt - 1"
//                            ), "lft BETWEEN " . $category->lft . " AND " . $category->rgt);
                }

//                $this->tableGateway->update(array(
//                    "lft" => "lft - 2"
//                        ), "lft > " . $category->rgt);
//
//                $this->tableGateway->update(array(
//                    "rgt" => "rgt - 2"
//                        ), "rgt > " . $category->rgt);

                $this->tableGateway->getAdapter()->getDriver()->getConnection()->execute("update category set lft = lft - 2 where lft > ".$category->rgt);
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->execute("update category set rgt = rgt - 2 where rgt > ".$category->rgt);
                
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
            } catch (\Exception $e) {
                $this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
            }
        }
    }

}