<?php

class Application_Model_HomePageMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_HomePage');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_HomePage $home_page)
    {
        $data = array(
            'id' => $home_page->getId(),
            'name'   => $home_page->getName(),
            'description' => $home_page->getDescription(),
            'img_url' => $home_page->getImgUrl(),
        );

        // 沒資料就新增
        try {
            $this->getDbTable()->insert($data);
        } catch (Zend_Exception $e) {
            // 如果資料已存在 db, 更新資料
            $this->getDbTable()->update($data, array('id = ?' => $home_page->getId()));
        }

    }

    public function find($id, Application_Model_HomePage $home_page)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return false;
        }

        $row = $result->current();
        $home_page->setId($row->id)
            ->setName($row->name)
            ->setDescription($row->description)
            ->setImgUrl($row->img_url);

        return $home_page;
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_HomePage();
            $entry->setId($row->id)
                ->setName($row->name)
                ->setDescription($row->description)
                ->setImgUrl($row->img_url);

            $entries[] = $entry;
        }
        return $entries;
    }

}
