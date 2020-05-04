<?php namespace application\core;

use application\util\i18next;
use PDO;
use PDOException;
use application\util\ControllerUtil;
use application\util\AppUtil as AppUtils;
use application\util\MessageUtils as MessageUtil;
use application\util\FilterUtils as FilterUtil;

class BaseDatabaseSupport
{

    private $dbh;


    private $stmt;

    //paging
    private $pagingLink;

    private $totalPaging=0;

    /**
     * @return int
     */
    public function getTotalPaging(): int
    {
        return $this->totalPaging;
    }

    /**
     * @param int $totalPaging
     */
    public function setTotalPaging(int $totalPaging)
    {
        $this->totalPaging = $totalPaging;
    }


    /**
     * @param mixed $dbh
     */
    public function setDbh($dbh)
    {
        $this->dbh = $dbh;
    }

    public function getDbh()
    {
        return $this->dbh;
    }

    public function query($query)
    {
        try {
            $this->stmt = $this->dbh->prepare($query);
        } catch (PDOException $e) {
            ControllerUtil::displaySqlError($e->getMessage());
        }
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            ControllerUtil::displaySqlError($e->getMessage());
            return false;
        }
    }
    public function list()
    {
        $this->execute();
        try {
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return [];
        }
    }
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount()
    {
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
    }

    public function lastInsertId()
    {
        try {
            return $this->dbh->lastInsertId();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
    }

    /* example
         * try {
                // First of all, let's begin a transaction
                $this->beginTransaction();

                // A set of queries; if one fails, an exception should be thrown
                $this->query('first query');
                $this->bind(":id", $id);
                $this->execute()

                $this->query('second query');
                $this->bind(":id", $id);
                $this->execute()

                $this->query('third query');
                $this->bind(":id", $id);
                $this->execute()

                // If we arrive here, it means that no exception was thrown
                // i.e. no query has failed, and we can commit the transaction
                $this->endTransaction();
        } catch (PDOException $e) {
                // An exception has been thrown
                // We must rollback the transaction
                $this->cancelTransaction();
        }
     */
    public function beginTransaction()
    {
        try {
            return $this->dbh->beginTransaction();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
    }

    public function endTransaction()
    {

        try {
            return $this->dbh->commit();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }

    }

    public function cancelTransaction()
    {
        try {
            return $this->dbh->rollBack();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
    }

    public function debugDumpParams()
    {

        try {
            return $this->stmt->debugDumpParams();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
    }


    public function getTableColunmName($tableName)
    {
        $this->query("DESCRIBE " . $tableName);
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTableColunmMetaData($tableName)
    {
//        $this->query("SHOW FULL COLUMNS FROM ".$tableName);
        $this->query("SHOW COLUMNS FROM " . $tableName);
        $this->execute();
        $resaultList = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        $returnList = array();
        if (!empty($this->list())) {
            foreach ($resaultList AS $resault) {
                $pharTypeLenght = $this->pharDbColunmType($resault['Type']);
                $resault['vType'] = $pharTypeLenght['vType'];
                $resault['vLength'] = $pharTypeLenght['vLength'];
                array_push($returnList, $resault);
            }

        }
        return $returnList;
    }

    private function pharDbColunmType($type)
    {

        $return['vType'] = '';
        $return['vLength'] = '';
        if (!AppUtils::isEmpty($type)) {
            $typeArr = explode("(", $type);
            if (count($typeArr) > 1) {
//	            print_r($typeArr);
                $return['vType'] = $typeArr[0];
                $return['vLength'] = substr($typeArr[1], 0, -1);
            } else {
                $return['vType'] = $typeArr[0];
                $return['vLength'] = '';
            }
        }

        return $return;

    }


    public function insertHelper($tableName, $data_array)
    {

        $columns = array();
        $bindColumns = array();
        foreach ($data_array as $key => $value) {
            $columns[] = $key;
            $bindColumns[] = ':' . $key;
        }
        $cols = implode(",", $columns);
        $bindParam = implode(",", $bindColumns);
        $query = "INSERT INTO `" . $tableName . "` (" . $cols . ") VALUES (" . $bindParam . ")";
        try {
            $this->query($query);
            foreach ($data_array as $key => $value) {
                $this->bind(':' . $key, $value);
            }
            $this->execute();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }

        return $this->lastInsertId();
    }

    public function insertObjectHelper($object)
    {
        if (!$object) {
            return false;
        }
        $tableFiedArray = $object->getTableField();
        $tableName = $object->getTableName();
        $autoIncrementType = $object->getAutoIncrementType();
        $booleanType = $object->getBooleanType();

        $columns = array();
        $bindColumns = array();
        foreach ($tableFiedArray as $key => $dataType) {

            if ($dataType != $autoIncrementType) {
                $columns[] = $key;
                $bindColumns[] = ':' . $key;
            }
        }
        $cols = implode(",", $columns);
        $bindParam = implode(",", $bindColumns);
        $query = "INSERT INTO `" . $tableName . "` (" . $cols . ") VALUES (" . $bindParam . ")";
//		echoln($query);
        try {
            $this->query($query);
            foreach ($tableFiedArray as $column => $columnType) {
                if ($columnType != $autoIncrementType && $columnType != $booleanType) {
                    $this->bind(':' . $column, $object->{$column});
                } else if ($columnType == $booleanType) {
                    $this->bind(':' . $column, $object->{$column});
                }
            }
            $this->execute();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
        return $this->lastInsertId();

    }

    public function updateHelper($tableName, $data_array, $where_array, $whereType)
    {

        $columnArr = array();

        $query = " UPDATE " . $tableName . " SET ";

        //colunm
        $rowNo = 0;
        foreach ($data_array as $column => $value) {
            $rowNo++;
            if ($rowNo == 1) {
                $query .= $column . "=" . ":" . $column;
            } else {
                $query .= "," . $column . "=" . ":" . $column;
            }

            $columnArr[] = $column;
        }
        $query .= " WHERE ";

        //where
        $whereNo = 0;
        foreach ($where_array as $where_column => $where_value) {


            $whereNo++;
            $where_column_query_bind = $where_column;
            if (in_array($where_column, $columnArr)) {
                $where_column_query_bind .= $whereNo;
            }
            if ($whereNo == 1) {
                $query .= $where_column . "=" . ":" . $where_column_query_bind;
            } else {
                $query .= " " . $whereType . " " . $where_column . "=" . ":" . $where_column_query_bind;
            }
//			if($whereNo==1){
//				$query .=$where_column."=".":".$where_column;
//			}else{
//				$query .=" ".$whereType." ".$where_column."=".":".$where_column;
//			}
        }
        try {
            $this->query($query);

            //bind colunm
            foreach ($data_array as $column_bind => $value_bind) {
                $this->bind(':' . $column_bind, $value_bind);
            }
            //bind where
            $whereValueNo = 0;
            foreach ($where_array as $where_column_bind => $where_value_bind) {
                $whereValueNo++;
                $where_column_value_bind = $where_column_bind;
                if (in_array($where_column_bind, $columnArr)) {
                    $where_column_value_bind .= $whereValueNo;
                }
                $this->bind(':' . $where_column_value_bind, $where_value_bind);
//				$this->bind(':'.$where_column_bind, $where_value_bind);
            }
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }
        return $this->execute();
    }

    public function updateObjectHelper($object, $where_array, $whereType)
    {

        if (!$object) {
            return false;
        }
        $columnArr = array();
        $tableFiedEditArray = $object->getTableFieldForEdit();
        $tableName = $object->getTableName();
        $booleanType = $object->getBooleanType();

        $query = " UPDATE " . $tableName . " SET ";

        //colunm
        $rowNo = 0;
        foreach ($tableFiedEditArray as $column => $value) {
            $rowNo++;
            if ($rowNo == 1) {
                $query .= $column . "=" . ":" . $column;
            } else {
                $query .= "," . $column . "=" . ":" . $column;
            }
            $columnArr[] = $column;
        }
        $query .= " WHERE ";

        //where
        $whereNo = 0;
        foreach ($where_array as $where_column => $where_value) {
            $whereNo++;

            $where_column_query_bind = $where_column;
            if (in_array($where_column, $columnArr)) {
                $where_column_query_bind .= $whereNo;
            }
            if ($whereNo == 1) {
                $query .= $where_column . "=" . ":" . $where_column_query_bind;
            } else {
                $query .= " " . $whereType . " " . $where_column . "=" . ":" . $where_column_query_bind;
            }
//			if($whereNo==1){
//				$query .=$where_column."=".":".$where_column;
//			}else{
//				$query .=" ".$whereType." ".$where_column."=".":".$where_column;
//			}
        }
        $this->query($query);

        //bind colunm
        foreach ($tableFiedEditArray as $column_bind => $columnType_bind) {
            if ($columnType_bind != $booleanType) {
                $this->bind(':' . $column_bind, $object->{$column_bind});
            } else if ($columnType_bind == $booleanType) {
                $this->bind(':' . $column_bind, $object->{$column_bind});
            }
        }
        //bind where
        $whereValueNo = 0;
        foreach ($where_array as $where_column_bind => $where_value_bind) {

            $whereValueNo++;

            $where_column_value_bind = $where_column_bind;
            if (in_array($where_column_bind, $columnArr)) {
                $where_column_value_bind .= $whereValueNo;
            }
            $this->bind(':' . $where_column_value_bind, $where_value_bind);
//			$this->bind(':'.$where_column_bind, $where_value_bind);
        }
        return $this->execute();
    }


    /*
     |Paging Helper
     */

    public function pagingHelper($query, $perpage, $where_array = null)
    {
        $this->query($query);

        //bind if not null
        if ($where_array) {
            foreach ($where_array as $where_column_bind => $where_value_bind) {
                $this->bind(':' . $where_column_bind, $where_value_bind);
            }
        }

        $this->execute();
        $totalRec = $this->rowCount();

//        $this->setPagingLink($totalRec, $perpage);
        $this->setTotalPages($totalRec, $perpage);

        return $this->getPagingLimitQuery($perpage);
    }

    public function getPagingLimitQuery($recordPerPage, $curentPage = 0)
    {
        $startingPosition = 0;
        $pagingParam = MessageUtil::getConfig('base_paging_param');
        if (FilterUtil::validateGetInt($pagingParam)) {
            $startingPosition = (FilterUtil::filterGetInt($pagingParam) - 1) * $recordPerPage;
        } elseif ($curentPage > 0) {
            $startingPosition = ($curentPage - 1) * $recordPerPage;
        }
        $queryPaging = " LIMIT $startingPosition,$recordPerPage";
        return $queryPaging;
    }

    /**
     * @return mixed
     */
    public function getPagingLink()
    {
        return $this->pagingLink;
    }

    /**
     * @param $total_no_of_records
     * @param $records_per_page
     * @return null|string
     */
    public function setPagingLink($total_no_of_records, $records_per_page)
    {
        $pageLink = '';
        $selfPageLing = null;
        $selfUri = null;
        $selfUriParam = null;
        if ($total_no_of_records > 0) {
            $self = FilterUtil::filterServerUrl('REQUEST_URI');
            $total_no_of_pages = ceil($total_no_of_records / $records_per_page);
            $current_page = 1;
            $pagingParam = MessageUtil::getConfig('base_paging_param');

            if (FilterUtil::validateGetInt($pagingParam)) {
                $current_page = FilterUtil::filterGetInt($pagingParam);

                $selfSplit = explode("?", $self);
                if (count($selfSplit) > 1) {
                    $selfUri = $selfSplit[0];
                    $selfUriParam = $selfSplit[1];
                    $selfUriParam = str_replace('page=' . $current_page, '', $selfUriParam);
                }
                $selfPageLing = $selfUri . '?' . $selfUriParam;
            } else {
                $selfSplit = explode("?", $self);
                if (count($selfSplit) > 1) {
                    $selfPageLing = $self . '&';
                } else {
                    $selfPageLing = $self . '?';
                }

            }

            if ($total_no_of_pages > 1) {
                $pageLink .= '<ul class="pagination">';
                $right_links = $current_page + 5;
                $previous = $current_page - 5; //previous link
                $next = $current_page + 1; //next link
                $first_link = true; //boolean var to decide our first link
                if ($current_page > 1) {
                    $previous_link = $current_page - 1;
                    $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=1" . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.frist') . "\">&laquo;</a></li>"; //first link
                    $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=" . $previous_link . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.previous') . "\">&lt;</a></li>"; //previous link
                    for ($i = ($current_page - 2); $i < $current_page; $i++) { //Create left-hand side links
                        if ($i > 0) {
                            $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=" . $i . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.page') . ' ' . $i . "\">" . $i . "</a></li>";
                        }
                    }
                    $first_link = false; //set first link to false
                }

                if ($first_link) { //if current active page is first link
                    $pageLink .= "<li class=\"page-item active\"><a class='page-link' href=\"\">" . $current_page . " <span class=\"sr-only\">(current)</span></a></li>";
                } elseif ($current_page == $total_no_of_pages) { //if it's the last active link
                    $pageLink .= "<li class=\"page-item active\"><a class='page-link' href=\"\">" . $current_page . " <span class=\"sr-only\">(current)</span></a></li>";
                } else { //regular current link
                    $pageLink .= "<li class=\"page-item active\"><a class='page-link' href=\"\">" . $current_page . " <span class=\"sr-only\">(current)</span></a></li>";
                }

                for ($i = $current_page + 1; $i < $right_links; $i++) { //create right-hand side links
                    if ($i <= $total_no_of_pages) {
                        $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=" . $i . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.page') . ' ' . $i . "\">" . $i . "</a></li>";
                    }
                }
                if ($current_page < $total_no_of_pages) {
//					$next_link = ($i > $total_no_of_pages)? $total_no_of_pages : $i;
                    $next_page = $current_page + 1;
                    $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=" . $next_page . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.next') . "\">&gt;</a></li>"; //next link
                    $pageLink .= "<li class=\"page-item\"><a class='page-link' href=\"" . $selfPageLing . $pagingParam . "=" . $total_no_of_pages . "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . i18next::getTranslation('paging.last') . "\">&raquo;</a></li>"; //last link
                }

                $pageLink .= '</ul>';
            }

        }
        $this->pagingLink = $pageLink;
    }
    public function setTotalPages($total_no_of_records, $records_per_page)
    {
        $total_no_of_pages = 0;
        if ($total_no_of_records > 0) {
            $total_no_of_pages = ceil($total_no_of_records / $records_per_page);
        }
        $this->setTotalPaging($total_no_of_pages);
    }
    public function genAdditionalParamAndWhereForListPage($q_parameter = array(), $ariasTableName=null, $manualSort= false, $defaultSortOrder = "DESC", $defaultSortField = "id")
    {

        $return = null;
        $query = "";
        $sortField = "";
        $sortOrder = "";
        if(!$manualSort){
            $sortField = " ORDER BY " . $ariasTableName . ".`" . $defaultSortField . "` ";
            $sortOrder = " " . $defaultSortOrder . " ";
        }


        $data_bind_where = null;

        if (!empty($q_parameter)) {
            foreach ($q_parameter AS $qKey => $qValue) {

                if ($qKey != 'sortMode' && $qKey != 'sortField') {
                    $fieldNameArr = explode("q_", $qKey);
                    $fieldName = (count($fieldNameArr) > 0) ? $fieldNameArr[1] : "";
                    if (!AppUtils::isEmpty($fieldName)) {
                        $query .= " AND " . $ariasTableName . ".`" . $fieldName . "` LIKE :" . $qKey . "  ";
                        $data_bind_where[$qKey] = "%" . $qValue . "%";//bind param for like query
                    }
                } else if ($qKey == 'sortField') {
                    $sortField = " ORDER BY " . $ariasTableName . ".`" . $qValue . "` ";
                } else if ($qKey == 'sortMode') {
                    $sortOrder = " " . $qValue . " ";
                }
            }
        }

        $return['additional_query'] = $query . $sortField . $sortOrder;
        $return['where_bind'] = $data_bind_where;

        return $return;

    }

    public function genBindParamAndWhereForListPage($where_array = array())
    {
        //bind if not null
        if (!empty($where_array)) {
            foreach ($where_array as $where_column_bind => $where_value_bind) {
                $this->bind(':' . $where_column_bind, $where_value_bind);
            }
        }
    }

    /*Memcahce Example*/
//	$memcache = memcache_connect('localhost', 11211);
//	$resaultList = null;
//	if ($memcache) {
//	$resaultList = $memcache->get($querykey);
//	}
//
//	if ($resaultList) {
//		echoln ("<p>Caching success!</p><p>Retrieved data from memcached!</p>");
//	}else{
//		//regular query
//		$this->query($query);
//		$resaultList =  $this->resultset();
//		$memcache->set($querykey, $resaultList, MEMCACHE_COMPRESSED, 600);
//		echoln("<p>Data not found in memcached.</p><p>Data retrieved from MySQL and stored in memcached for next time.</p>");
//	}


    //sqlsrv
    public function sqlsrvInsertHelper($tableName, $data_array)
    {

        $columns = array();
        $bindColumns = array();
        foreach ($data_array as $key => $value) {
            $columns[] = $key;
            $bindColumns[] = ':' . $key;
        }
        $cols = implode(",", $columns);
        $bindParam = implode(",", $bindColumns);
        $query = "INSERT INTO " . $tableName . " (" . $cols . ") VALUES (" . $bindParam . ")";
        try {
            $this->query($query);
            foreach ($data_array as $key => $value) {
                $this->bind(':' . $key, $value);
            }
            $this->execute();
        } catch (PDOException $e) {

            ControllerUtil::displaySqlError($e->getMessage());
            return false;

        }

        return $this->lastInsertId();
    }
}