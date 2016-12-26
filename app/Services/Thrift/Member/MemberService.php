<?php 
namespace App\Services\Thrift\Member;

use Thrift\Client\Member\MemberBase;
use Thrift\Client\Member\Credentials;
use Thrift\Client\Member\MemberDevice;
use Thrift\Client\Member\MemberProfile;
use Thrift\Client\Member\THBaseException;
use Thrift\Client\Member\MemberThirdparty;
use Thrift\Client\Member\MemberAddress;
use Thrift\Client\Member\MemberCrdl;
use Thrift\Client\Member\MemberVipcardBase;
use Thrift\Client\Member\MemberIdentity;
use Thrift\Client\Member\MemberSetting;
use Thrift\Client\Member\MemberTag;
use Thrift\Client\Member\MemberGroupRelation;
use Thrift\Client\Member\MemberGroup;

use App\Library\Log\Logger;

/**
 * THBase 会员接口服务
 *
 * @author 卢东栋
 * @version 1.0.0
 *
 * Class MemberService
 * @package Services\Thbase\Member
 */
class MemberService extends MemberBaseService {

    /**
     * =================================================================================================================
     * Member Auth
     */

    /**
     * 根据用户凭证，获取用户通用信息。
     *
     * @param array $credentials
     * @return mixed
     * @throws \Exception
     */
    public function retrieveByCredential(array $credentials) {
        try {
            return $this->client->retrieveByCredential(new Credentials($credentials));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 修改认证数据
     *
     * @param array $crdl
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberCrdl(array $crdl) {
        try {
            return $this->client->modifyMemberCrdl(new MemberCrdl($crdl));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 新增认证数据
     *
     * @param array $crdl
     * @return mixed
     * @throws \Exception
     */
    public function addMemberCrdl(array $crdl) {
        try {
            return $this->client->addMemberCrdl(new MemberCrdl($crdl));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * =================================================================================================================
     * Member Common
     */

    /**
     * 根据会有ID，获取会有通用信息。
     *
     * @param $memberId
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberCommon($memberId) {
        try {
            return $this->client->fetchMemberCommon($memberId);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 通过手机号，获取会员通用信息。
     *
     * @param $phone
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberCommonWithPhone($phone) {
        try {
            return $this->client->fetchMemberCommonWithPhone($phone);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 通过第三方ID和平台，获取会员通用信息
     *
     * @param $openid
     * @param $platform
     */
    public function fetchMemberCommonWithOpenid($openid, $platform) {
        try {
            return $this->client->fetchMemberCommonWithOpenid($openid,$platform);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * =================================================================================================================
     * Member Base
     */

    /**
     * 新增会员基础数据。
     *
     * @param array $memberBase
     * @return mixed
     * @throws \Exception
     */
    public function addMemberBase(array $memberBase) {
        try {
            return $this->client->addMemberBase(new MemberBase($memberBase));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }


    /**
     * =================================================================================================================
     * Member Device
     */

    /**
     * 添加用户设备
     *
     * @param array $memberDevice
     * @return mixed
     * @throws \Exception
     */
    public function addMemberDevice(array $memberDevice) {

        try {
            return $this->client->addMemberDevice(new MemberDevice($memberDevice));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 更新会员设备（仅更新设备的唯一标示）
     *
     * @param array $memberDevice
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberDevice(array $memberDevice) {

        try {
            return $this->client->modifyMemberDevice(new MemberDevice($memberDevice));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 获取会员设备信息
     *
     * @param $memberId
     *
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberDevice($memberId) {

        try {
            return $this->client->fetchMemberDevice($memberId);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * =================================================================================================================
     * Member Profile
     */

    /**
     * 获取会员的平台资料信息
     *
     * @param $member_id
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberProfile($member_id) {
        try {
            return $this->client->fetchMemberProfile($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 添加会员资料
     *
     * @param array $profile
     * @return mixed
     * @throws \Exception
     */
    public function addMemberProfile(array $profile) {
        try {
            return $this->client->addMemberProfile(new MemberProfile($profile));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * =================================================================================================================
     * Member Third Party
     */

    /**
     * 通过平台和第三方ID，获取第三方账户信息。
     *
     * @param $openid
     * @param $platform
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberThirdpartyWithOpenID($openid, $platform) {
        try {
            return $this->client->fetchMemberThirdpartyWithOpenID($openid, $platform);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }



    /**
     * 添加第三方平台账户
     *
     * @param array $thirdparty
     * @return mixed
     * @throws \Exception
     */
    public function addMemberThirdparty(array $thirdparty) {
        try {
            if (!$thirdparty['unionid']) {
                unset($thirdparty['unionid']);
            }
            return $this->client->addMemberThirdparty(new MemberThirdparty($thirdparty));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 更新第三方平台账户
     *
     * @param array $thirdparty
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberThirdparty(array $thirdparty) {
        try {
            if (!$thirdparty['unionid']) {
                unset($thirdparty['unionid']);
            }
            return $this->client->modifyMemberThirdparty(new MemberThirdparty($thirdparty));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * =================================================================================================================
     * Member Identity
     */

    /**
     * 获取会员实名认证数据
     *
     * @param $member_id
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberIdentity($member_id) {
        try {
            return $this->client->fetchMemberIdentity($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
   
    
    /**
     * 更新会员实名认证数据
     * @param type $data
     */
    public function modifyMemberIdentity(array $identity) {
        try {
            return $this->client->modifyMemberIdentity( new MemberIdentity ($identity) );
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
  
    
    /**
     * 添加会员实名认证数据
     * @param type $data
     */
    public function addMemberIdentity(array $identity) {
        try {
            return $this->client->addMemberIdentity( new MemberIdentity($identity) );
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    
     public function fetchDeMemberIdentity($member_id) {
        try {
            return $this->client->fetchDeMemberIdentity($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
      public function modifyDeMemberIdentity(array $identity) {
        try {
            return $this->client->modifyDeMemberIdentity( new MemberIdentity ($identity) );
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    public function addDeMemberIdentity(array $identity) {
        try {
            return $this->client->addDeMemberIdentity( new MemberIdentity($identity) );
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * =================================================================================================================
     * Member Address
     */

    public function addMemberAddress(array $address) {
        try {
            return $this->client->addMemberAddress(new MemberAddress($address));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 获取会员默认的收件地址
     *
     * @param $member_id
     * @param $type
     *
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberDefaultAddress($member_id, $type) {
        try {
            return $this->client->fetchMemberDefaultAddress($member_id, $type);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 获取会员收件地址列表
     *
     * @param $member_id
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberAddresses($member_id, $type) {
        try {
            return $this->client->fetchMemberAddresses($member_id, $type);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 获取会员地址详情
     * @return type
     * @throws \Exception
     */
    public function fetchOneMemberAddress($member_address_id){
        try {
            return $this->client->fetchOneMemberAddress($member_address_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * =================================================================================================================
     * Member Setting
     */

    /**
     * 获取平台设置项数据
     *
     * @param $member_id
     * @param $platform
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberSetting($member_id, $platform, $key) {
        try {
            return $this->client->fetchMemberSetting($member_id, $platform, $key);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    
    public function modifyMemberSetting(array $sertting){
        try {
            return $this->client->modifyMemberSetting(new MemberSetting($sertting));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    public function addMemberSetting(array $sertting){
        try {
            return $this->client->addMemberSetting(new MemberSetting($sertting));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 编辑收件地址数据
     *
     * @param array $address
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberAddress(array $address) {
        try {
            return $this->client->modifyMemberAddress(new MemberAddress($address));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 获取会员认证数据
     *
     * @param $username
     * @param $platform
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberCrdl($username, $platform) {

        try {
            return $this->client->fetchMemberCrdl($username, $platform);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 修改会员基础数据
     *
     * @param array $member
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberBase(array $member) {

        try {
            return $this->client->modifyMemberBase(new MemberBase($member));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 新增会员卡基础数据
     *
     * @param array $vipcard
     * @return mixed
     * @throws \Exception
     */
    public function addMemberVipcardBase(array $vipcard) {

        try {
            return $this->client->addMemberVipcardBase(new MemberVipcardBase($vipcard));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 修改卡信息
     *
     * @param array $vipcard
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberVipcardBase(array $vipcard) {

        try {
            return $this->client->modifyMemberVipcardBase(new MemberVipcardBase($vipcard));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 通过会员ID，获取会员卡基础数据。
     *
     * @param $member_id
     * @return mixed
     * @throws \Exception
     */
    public function fetchMemberVipcardBase($member_id) {

        try {
            return $this->client->fetchMemberVipcardBase($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 通过卡号获取 会员卡积分消费记录
     * 
     * @param type $card_no
     * @return type
     * @throws \Exception
     */
    public function fetchMemberScoreLog($card_no){
         try {
            return $this->client->fetchMemberScoreLog($card_no);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 修改会员资料
     *
     * @param array $profile
     * @return mixed
     * @throws \Exception
     */
    public function modifyMemberProfile(array $profile) {

        try {
            return $this->client->modifyMemberProfile(new MemberProfile($profile));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 通过会员卡号获取会员卡数据信息
     * @param type $card_no
     * @return type
     * @throws \Exception
     */
    public function fetchMemberVipcardBaseByCardNo($card_no){
         try {
            return $this->client->fetchMemberVipcardBaseByCardNo($card_no);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    
    
    
    
    /**************************/
    public function fetchMemberCrdlList($member_id) {
        try {
            return $this->client->fetchMemberCrdlList($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    //跟modifyMemberCrdl有重复
    public function modifyMemberPassword($member_id,$new_password,$old_password){
         try {
            return $this->client->modifyMemberPassword($member_id,$new_password,$old_password);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    public function fetchMemberCrdlListWithUsername($username){
          try {
            return $this->client->fetchMemberCrdlWithUsername($username);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
        
    }
    
    
    function fetchAuth($username,$password){
         try {
            return $this->client->fetchAuth($username,$password);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    function fetchMemberCardBaseWithCardSysID($card_sys_id){
        try {
            return $this->client->fetchMemberVipcardBaseByCardSysID($card_sys_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    function fetchMemberScoreLogs($card_no,$page,$rowNum){
         try {
            return $this->client->fetchMemberScoreLogs($card_no,$page,$rowNum);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
        
    }
    
    function addMemberScoreLog(array $scorelog){
         try {
            return $this->client->addMemberScoreLog(new \Thrift\Client\Member\MemberScoreLog($scorelog));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /******************************会员标签操作******************************/
    
    /**
     * 通过会员id获取会员标签列表数据
     * @param type $member_id
     * @return type
     * @throws \Exception
     */
    public function fetchMemberTagsByMemberID($member_id){
         try {
            return $this->client->fetchMemberTagsByMemberID($member_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 通过标签id获取会员标签列表数据
     * @param type $tag_id
     * @return type
     * @throws \Exception
     */
    public function fetchMemberTagsByTagID($tag_id){
         try {
            return $this->client->fetchMemberTagsByTagID($tag_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * 添加会员标签数据
     * @param array $memberTag
     * @return type
     * @throws \Exception
     */
    public function addMemberTag(array $memberTag){
         try {
            return $this->client->addMemberTag(new MemberTag($memberTag));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 更新会员标签数据
     * @param array $memberTag
     * @return type
     * @throws \Exception
     */
    public function modifyMemberTag(array $memberTag){
         try {
            return $this->client->modifyMemberTag(new MemberTag($memberTag));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 用户主键删除会员标签数据（一条）
     * @param type $member_tag_id
     * @return type
     * @throws \Exception
     */
    public function deleteMemberTag($member_tag_id){
         try {
            return $this->client->deleteMemberTag($member_tag_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 通过标签id删除会员标签数据（多条）
     * @param type $tag_id
     * @return type
     * @throws \Exception
     */
    public function deleteMemberTagByTagID($tag_id){
         try {
            return $this->client->deleteMemberTagByTagID($tag_id);
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 添加会员组关系
     * @param type $data
     * @return type
     * @throws \Exception
     */
    public function addMemberGroupRelation($data){
        try {
            return $this->client->addMemberGroupRelation(new MemberGroupRelation( $data ));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
    
    /**
     * 添加会员组
     * @param type $data
     * @return type
     * @throws \Exception
     */
    public function addMemberGroup($data){
        try {
            return $this->client->addMemberGroup(new MemberGroup( $data ));
        } catch (THBaseException $ex) {
            Logger::error(var_export($ex, true), basename(__FILE__, '.php'));
            throw new \Exception($ex->getMessage(), $ex->getCode());
        } 
    }
    
}
