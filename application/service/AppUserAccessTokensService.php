<?php

namespace application\service;

use application\core\DatabaseSupport;
use application\serviceInterface\AppUserAccessTokensServiceInterface;
use application\model\AppUserAccessTokens;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\JWT;
use application\util\MessageUtils;
use application\util\SecurityUtil;
use application\util\SystemConstant;

class AppUserAccessTokensService extends DatabaseSupport implements AppUserAccessTokensServiceInterface
{
    protected $tableName = 'app_user_access_tokens';

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .= "FROM app_user_access_tokens AS app_user_access_tokens ";

        //where query
        $query .= " WHERE app_user_access_tokens.`id` IS NOT NULL ";

        //gen additional query and sort order
        $additionalParam = $this->genAdditionalParamAndWhereForListPage($q_parameter, $this->tableName);
        if (!empty($additionalParam)) {
            if (!empty($additionalParam['additional_query'])) {
                $query .= $additionalParam['additional_query'];
            }
            if (!empty($additionalParam['where_bind'])) {
                $data_bind_where = $additionalParam['where_bind'];
            }
        }

        //paging buider
        if ($perpage > 0) {
            $query .= $this->pagingHelper($query, $perpage, $data_bind_where);
        }
        //regular query
        $this->query($query);

        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
        //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='
        //END BIND VALUE FOR REGULAR QUERY

        //bind param for search param
        $this->genBindParamAndWhereForListPage($data_bind_where);

        //Return as Object Class
        /*
        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserAccessTokens($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        */
        return $this->resultset();
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM app_user_access_tokens AS app_user_access_tokens ";
        $query .= "WHERE app_user_access_tokens.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        //Return as Object Class
        /*
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $appUserAccessTokens = new AppUserAccessTokens($result);
            return $appUserAccessTokens;
        }
        */
        return $this->single();
    }

    public function findByToken($token, $onlyActive = false)
    {
        $query = "SELECT *  ";
        $query .= "FROM app_user_access_tokens AS app_user_access_tokens ";
        $query .= "WHERE app_user_access_tokens.`token`=:token ";
        if ($onlyActive) {
            $query .= "AND app_user_access_tokens.`revoked`=0 ";
        }
        $this->query($query);
        $this->bind(":token", (int)$token);
        return $this->single();
    }

    public function findAllByAppUser($id, $onlyActive = false)
    {
        $query = "SELECT *  ";
        $query .= "FROM app_user_access_tokens AS app_user_access_tokens ";
        $query .= "WHERE app_user_access_tokens.`app_user`=:app_user ";
        if ($onlyActive) {
            $query .= "AND app_user_access_tokens.`revoked`=0 ";
        }
        $this->query($query);
        $this->bind(":app_user", (int)$id);
        return $this->resultset();
    }

    public function deleteById($id)
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }

    public function createNewToken(string $key, int $uid, int $apiClient, string $secretKey): string
    {
        $expireDatetime = DateUtils::plusDateByYear(DateUtils::dateNow(), 1);
        $state = $this->createByArray([
            'token' => $key,
            'app_user' => $uid,
            'api_client' => $apiClient,
            'expires_at' => DateUtils::getDateByDateFormat($expireDatetime),
            'name' => FilterUtils::filterServer('HTTP_USER_AGENT'),
            'created_date' => DateUtils::getDateNow(),
            'updated_date' => DateUtils::getDateNow(),
        ]);
        return $state ? JWT::encode([
            'uid' => $uid,
            'key' => $key,
            "iat" => DateUtils::getTimeNow(),
            "exp" => $expireDatetime->getTimestamp(),
        ], $secretKey) : null;
    }

    public function createByArray($data_array)
    {
        return $this->insertHelper($this->tableName, $data_array);
    }

    public function createByObject($oject)
    {
        return $this->insertObjectHelper($oject);
    }

    public function update($data_array, $where_array, $whereType = 'AND')
    {
        return $this->updateHelper($this->tableName, $data_array, $where_array, $whereType);
    }

    public function updateByObject($object, $where_array, $whereType = 'AND')
    {
        return $this->updateObjectHelper($object, $where_array, $whereType);
    }

    //logout by token key
    public function logoutAction()
    {
        $jwt = SecurityUtil::decodeJWT(false);
        $payload = $jwt['payload'];
        if (empty($payload)) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('httpStatus.401'),
            ], 401);
        }
        $accessTokenInDb = $this->findByToken($payload->key, true);

        $efectRow = 0;
        if ($accessTokenInDb) {
            $this->update(['revoked' => 1, 'updated_date' => DateUtils::getDateNow()], ['id' => $accessTokenInDb['id']]);
        }
        return $efectRow;
    }

    //user logout all
    public function logoutAllAction()
    {
        $jwt = SecurityUtil::decodeJWT(false);
        $payload = $jwt['payload'];
        if (empty($payload)) {
            jsonResponse([
                SystemConstant::SERVER_STATUS_ATT => false,
                SystemConstant::SERVER_MSG_ATT => i18next::getTranslation('httpStatus.401'),
            ], 401);
        }
        return $this->update(['revoked' => 1, 'updated_date' => DateUtils::getDateNow()], ['app_user' => $payload->uid, 'revoked' => 0]);
    }
}