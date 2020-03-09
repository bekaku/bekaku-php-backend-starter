<?php
namespace application\model;

use application\core\AppModel as BaseModel;
class Configuration extends BaseModel
{
    public static $tableName = 'configuration';

    private $name;
    private $name_eng;
    private $address;
    private $web;
    private $phone_no;
    private $img_name;
    private $fax;
    private $e_mail;
    private $secret_key;
    private $map_latitude;
    private $map_longtitude;

    /*optional field*/

    public function __construct($data = array())
    { 
        /* init data type for field*/
        $this->setTableField(array(
            'id' => self::TYPE_AUTO_INCREMENT,
            'name' => self::TYPE_STRING,
            'name_eng' => self::TYPE_STRING,
            'address' => self::TYPE_STRING,
            'web' => self::TYPE_STRING,
            'phone_no' => self::TYPE_STRING,
            'img_name' => self::TYPE_STRING,
            'fax' => self::TYPE_STRING,
            'e_mail' => self::TYPE_STRING,
            'secret_key' => self::TYPE_STRING,
            'map_latitude' => self::TYPE_STRING,
            'map_longtitude' => self::TYPE_STRING,
        )); 
 
        /* init data type for field use in update mode*/
        $this->setTableFieldForEdit(array(
            'name' => self::TYPE_STRING,
            'name_eng' => self::TYPE_STRING,
            'address' => self::TYPE_STRING,
            'web' => self::TYPE_STRING,
            'phone_no' => self::TYPE_STRING,
            'img_name' => self::TYPE_STRING,
            'fax' => self::TYPE_STRING,
            'e_mail' => self::TYPE_STRING,
            'secret_key' => self::TYPE_STRING,
            'map_latitude' => self::TYPE_STRING,
            'map_longtitude' => self::TYPE_STRING,

            'updated_user' => self::TYPE_INTEGER,
            'updated_date' => self::TYPE_DATE_TIME
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
    public function getName()
    { 
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNameEng()
    { 
        return $this->name_eng;
    }

    /**
     * @param mixed $name_eng
     */
    public function setNameEng($name_eng)
    {
        $this->name_eng = $name_eng;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    { 
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getWeb()
    { 
        return $this->web;
    }

    /**
     * @param mixed $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return mixed
     */
    public function getPhoneNo()
    { 
        return $this->phone_no;
    }

    /**
     * @param mixed $phone_no
     */
    public function setPhoneNo($phone_no)
    {
        $this->phone_no = $phone_no;
    }

    /**
     * @return mixed
     */
    public function getImgName()
    { 
        return $this->img_name;
    }

    /**
     * @param mixed $img_name
     */
    public function setImgName($img_name)
    {
        $this->img_name = $img_name;
    }

    /**
     * @return mixed
     */
    public function getFax()
    { 
        return $this->fax;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getEMail()
    { 
        return $this->e_mail;
    }

    /**
     * @param mixed $e_mail
     */
    public function setEMail($e_mail)
    {
        $this->e_mail = $e_mail;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * @param mixed $secret_key
     */
    public function setSecretKey($secret_key)
    {
        $this->secret_key = $secret_key;
    }

    /**
     * @return mixed
     */
    public function getMapLatitude()
    { 
        return $this->map_latitude;
    }

    /**
     * @param mixed $map_latitude
     */
    public function setMapLatitude($map_latitude)
    {
        $this->map_latitude = $map_latitude;
    }

    /**
     * @return mixed
     */
    public function getMapLongtitude()
    { 
        return $this->map_longtitude;
    }

    /**
     * @param mixed $map_longtitude
     */
    public function setMapLongtitude($map_longtitude)
    {
        $this->map_longtitude = $map_longtitude;
    }

}