<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppStatisticMonth extends BaseModel
{
    public static $tableName = 'app_statistic_month';

    private $month_view;
    private $month_no;
    private $year_no;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'month_view' => self::TYPE_INTEGER,
            'month_no' => self::TYPE_INTEGER,
            'year_no' => self::TYPE_INTEGER,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'month_view' => self::TYPE_INTEGER,
            'month_no' => self::TYPE_INTEGER,
            'year_no' => self::TYPE_INTEGER,

        ));

        /* init optional field*/
        $this->setTableOptionalField(array(
            //'field_name_option',
        ));

        $this->populate($data, $this);
        $this->populateBase($data);
    }

    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * @return mixed
     */
    public function getMonthView()
    { 
        return $this->month_view;
    }

    /**
     * @param mixed $month_view
     */
    public function setMonthView($month_view)
    {
        $this->month_view = $month_view;
    }

    /**
     * @return mixed
     */
    public function getMonthNo()
    { 
        return $this->month_no;
    }

    /**
     * @param mixed $month_no
     */
    public function setMonthNo($month_no)
    {
        $this->month_no = $month_no;
    }

    /**
     * @return mixed
     */
    public function getYearNo()
    { 
        return $this->year_no;
    }

    /**
     * @param mixed $year_no
     */
    public function setYearNo($year_no)
    {
        $this->year_no = $year_no;
    }

}