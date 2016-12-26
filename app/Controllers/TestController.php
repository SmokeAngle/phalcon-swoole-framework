<?php
namespace App\Controllers;

use Phalcon\Di;
use App\Controllers\BaseController;
use Phalcon\Mvc\Controller;
use App\Models\Test as ModelTest;
use App\Library\Common\Response;
use App\Library\Payment\Netpay;

class TestController extends BaseController {
    
    
    public function test1() {       
        
//        for( $i = 0; $i < 400; $i ++ ) {
//            var_dump($this->redisService->mainCluster->get('wx:StoreGoodsDaKey:1700971'));
//        }
//        
//        $this->dbService->default->fetchOne($sqlQuery);
//            var_dump($this->redisService->mainCluster->get('wx:StoreGoodsDaKey:1700971'));
//        var_dump($this->thfirtService->MemberService->fetchDeMemberIdentity(522489));   
//        var_dump($this->redisService->common->ping());
//        $this->request->get('a', NULL, FALSE)
//        var_dump($this->dbService->default);
//        var_dump($this->amqpService);
//        $result = Di::getDefault()->getShared('dbService')->default->query("SELECT sum(total_pay_amount) as total_amount FROM `order` where order_id = 1")->fetch(\PDO::FETCH_ASSOC);
//        var_dump($result['total_amount']);
//        var_dump(ModelTest::find(array("order_id=38"))->toArray());
//        var_dump(ModelTest::test());
//        var_dump((Di::getDefault()->getShared('dbService')->default));die();
        //var_dump(Di::getDefault()->getShared('dbService')->getConnectionServiceName('default'));
//        var_dump(Di::getDefault()->getShared('amqpService'));
//        Response::responseJson($code, $message, $data);
//        var_dump($this->amqpService->getInstance());
//        var_dump(\Omnipay\Omnipay::create('WechatPay_App'));
        
//        $orderNo = '538791601000074';
//        $sql = 'SELECT * FROM `order` WHERE order_no = :order_no';
//        $orderResult = Di::getDefault()->getShared('dbService')->default->fetchOne($sql, \Phalcon\Db::FETCH_ASSOC, array('order_no' => $orderNo));
//        var_dump($orderResult);die();

//       var_dump(\App\Models\Order::getOrderNo());die();
        
        
//        var_dump($this->redisService->common->get('a'));
        
//        Netpay::
        
//        var_dump($this->config->pay->cmbNetpay);die();
//        $config = array(
//            "version"   => "1.0",
//            "charset"   => "UTF-8",
//            "sign"      => "",
//            "signType"  => "SHA-256",
//            'reqData'   => array(
//                'extendInfoEncrypType'  => 'RC4',
//                'extendInfo'            => ''
//            )
//        );
//        $orderId = 1607456;
//        $orderNo = '556331601000079';
//        $isBatch = TRUE;
//        $payType = 4;
//        var_dump(\App\Models\Order::getOutTradeNo($orderId, $orderNo, $isBatch, $payType));
//        die();
//        $payConfig  = $this->config->pay->cmbNetpay;
//        $branchNo   = $payConfig->get('BRANCH_NO', FALSE);
//        $merchantNo = $payConfig->get('MERCHANT_NO', FALSE);
//        $signKey    = $payConfig->get('SIGN_KEY', FALSE);
//        
//        $notifyUrl = $payConfig->get('NOTIFY_URL', FALSE);
//        $returnUrl = $payConfig->get('NOTIFY_URL', FALSE);
//        $signNoticeUrl = $payConfig->get('NOTIFY_URL', FALSE);
//        
//        $currentTimeStamp = time();
//        $amount = 10.09;
//        $netPay = Netpay::createPayRequest($payConfig);
//        $netPay->setSignKey($signKey);
//        $netPay->setBranchNo($branchNo);
//        $netPay->setMerchantNo($merchantNo);
//        $netPay->setNoticeUrl($notifyUrl);
//        $netPay->setReturnUrl($returnUrl);
//        $netPay->setSignNoticeUrl($signNoticeUrl);
//        
//        $netPay->setPayload(array(
//            'dateTime'          => date('YmdHis', $currentTimeStamp),
//            'date'              => date('Ymd', $currentTimeStamp),
//            'orderNo'           => '',
//            'amount'            => $amount,
//            'payNoticePara'     => '',
//            'agrNo'             => '',
//            'merchantSerialNo'  => '',
//            'signNoticePara'    => '',
//        ));
//        $payload = $netPay->getPayload();
//        return Response::responseJson($orderNo, $message, $extraData);
        
//            $this->response->setStatusCode('403');
//            $this->response->setContent('fail');
//            
//            $this->response->send();
        
            $orderList = \App\Models\Order::find('order_id IN (1605875, 1605877, 1605970)'); // 获取订单
            
//            $orderList = \App\Models\Order::find('order_id IN (9999)'); // 获取订单
            $orderListArr =  FALSE != $orderList ? $orderList->toArray() : array();
            $totalPay = array_map(function( $dataRow ) {
                return $dataRow['total_pay_amount'];
            }, $orderListArr);
            $totalPay = array_sum($totalPay);
            var_dump($totalPay);die();
    }
    
    public function pay() {
       
    }
    
}
