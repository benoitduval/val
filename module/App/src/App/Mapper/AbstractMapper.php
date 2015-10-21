<?php

namespace App\Mapper;

class AbstractMapper
{

    protected $_tableGateway;
    protected $_entity;

    public function fetchAll($where = array(), $orderBy = 'id DESC')
    {
        return $this->getTableGateway()->select(function($select) use ($orderBy, $where) {
            $select->where($where)->order($orderBy);
        });
    }

    public function fetchOne($where = array())
    {
        $resultSet = $this->getTableGateway()->select($where);
        foreach ($resultSet as $result) {
            return $result;
        }
    }

    public function getById($id)
    {
        $resultRow = $this->getTableGateway()->select(array('id' => (int) $id));
        $row = $resultRow->current();
        return $row;
    }

    public function save()
    {
        if (!(int) $this->getEntity()->id) {
            $this->getTableGateway()->insert($this->getEntity()->toArray());
            $this->getEntity()->id = $this->getTableGateway()->lastInsertValue;
        } else {
            if ($this->getById($this->getEntity()->id)) {
                $data = $this->getEntity()->toArray();
                $this->getTableGateway()->update($data, array('id' => $this->getEntity()->id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return $this->getEntity();
    }

    public function delete($id)
    {
        $this->getTableGateway()->delete(array('id' => $id));
    }

    public function setTableGateway($tableGateway)
    {
        $this->_tableGateway = $tableGateway;
    }

    public function getTableGateway()
    {
        return $this->_tableGateway;
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function fromArray($data)
    {
        $this->getEntity()->exchangeArray($data);
        return $this;
    }
}