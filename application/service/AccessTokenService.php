<?php

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\model\AccessToken;
use application\serviceInterface\AccessTokenServiceInterface;
use application\util\DateUtils;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\JWT;
use application\util\SecurityUtil;
use application\util\SystemConstant;

class AccessTokenService extends BaseDatabaseSupport implements AccessTokenServiceInterface
{
    protected $tableName = 'access_token';
    /**
     * @var UserAgentService
     */
    private $userAgentService;

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
        $this->userAgentService = new UserAgentService($this->getDbh());
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .= "FROM access_token AS access_token ";

        //default where query
        $query .= " WHERE access_token.`id` IS NOT NULL ";
        //custom where query
        //$query .= "WHERE access_token.custom_field =:customParam ";

        //gen additional query and sort order
        $additionalParam = $this->genAdditionalParamAndWhereForListPageV2($q_parameter, new AccessToken());
        if (!empty($additionalParam)) {
            if (!empty($additionalParam['additional_query'])) {
                $query .= $additionalParam['additional_query'];
            }
            if (!empty($additionalParam['where_bind'])) {
                $data_bind_where = $additionalParam['where_bind'];
            }
        }

        //custom where paramiter
        // $data_bind_where['custom_field']=$paramValue;
        //end
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

        return $this->list();
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM access_token AS access_token ";
        $query .= "WHERE access_token.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->single();
    }

    public function findByToken($token, $onlyActive = false)
    {
        $query = "SELECT *  ";
        $query .= "FROM access_token ";
        $query .= "WHERE `token`=:token ";
        if ($onlyActive) {
            $query .= "AND `revoked`=0 ";
        }
        $this->query($query);
        $this->bind(":token", (int)$token);
        return $this->single();
    }

    public function findAllByAppUser($id, $onlyActive = false)
    {
        $query = "SELECT *  ";
        $query .= "FROM access_token ";
        $query .= "WHERE `app_user`=:app_user ";
        if ($onlyActive) {
            $query .= "AND `revoked`=0 ";
        }
        $this->query($query);
        $this->bind(":app_user", (int)$id);
        return $this->list();
    }

    public function deleteById($id)
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }

    public function deleteByToken($token)
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE token=:token";
        $this->query($query);
        $this->bind(":token", (string)$token);
        return $this->execute();
    }

    public function createNewToken(string $key, int $uid, int $apiClient, string $secretKey): string
    {
        $userAgentName = FilterUtils::filterServer('HTTP_USER_AGENT');
        $userAgent = $this->userAgentService->findByName($userAgentName);
        $userAgentId = null;
        if (!$userAgent) {
            $userAgentId = $this->userAgentService->createByArray([
                'agent' => $userAgentName,
            ]);
        } else {
            $userAgentId = $userAgent->id;
        }


        $expireDatetime = DateUtils::plusDateByYear(DateUtils::dateNow(), 1);
        $state = $this->createByArray([
            'token' => $key,
            'user' => $uid,
            'api_client' => $apiClient,
            'expires_at' => DateUtils::getDateByDateFormat($expireDatetime),
            'user_agent' => $userAgentId,
            'created_at' => DateUtils::getDateNow(),
            'updated_at' => DateUtils::getDateNow(),
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
            $this->update(['revoked' => 1, 'updated_at' => DateUtils::getDateNow()], ['id' => $accessTokenInDb->id]);
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
        return $this->update(['revoked' => 1, 'updated_at' => DateUtils::getDateNow()], ['user' => $payload->uid, 'revoked' => 0]);
    }

}