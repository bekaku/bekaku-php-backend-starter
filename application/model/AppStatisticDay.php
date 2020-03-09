<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class AppStatisticDay extends BaseModel
{
    public static $tableName = 'app_statistic_day';

    private $ip_add;
    private $ss_id;
    private $count_date;
    private $user_angine;
    private $isBot;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'ip_add' => self::TYPE_STRING,
            'ss_id' => self::TYPE_STRING,
            'count_date' => self::TYPE_STRING,
            'user_angine' => self::TYPE_STRING,
            'isBot' => self::TYPE_STRING,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'ip_add' => self::TYPE_STRING,
            'ss_id' => self::TYPE_STRING,
            'count_date' => self::TYPE_STRING,
            'user_angine' => self::TYPE_STRING,
            'isBot' => self::TYPE_INTEGER,

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
    public function getIpAdd()
    { 
        return $this->ip_add;
    }

    /**
     * @param mixed $ip_add
     */
    public function setIpAdd($ip_add)
    {
        $this->ip_add = $ip_add;
    }

    /**
     * @return mixed
     */
    public function getSsId()
    { 
        return $this->ss_id;
    }

    /**
     * @param mixed $ss_id
     */
    public function setSsId($ss_id)
    {
        $this->ss_id = $ss_id;
    }

    /**
     * @return mixed
     */
    public function getCountDate()
    { 
        return $this->count_date;
    }

    /**
     * @param mixed $count_date
     */
    public function setCountDate($count_date)
    {
        $this->count_date = $count_date;
    }

    /**
     * @return mixed
     */
    public function getUserAngine()
    { 
        return $this->user_angine;
    }

    /**
     * @param mixed $user_angine
     */
    public function setUserAngine($user_angine)
    {
        $this->user_angine = $user_angine;
    }

    /**
     * @return mixed
     */
    public function getIsBot()
    { 
        return $this->isBot;
    }

    /**
     * @param mixed $isBot
     */
    public function setIsBot($isBot)
    {
        $this->isBot = $isBot;
    }

}