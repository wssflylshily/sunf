<?php
/**
 * Created by PhpStorm.
 * User: sunfan
 * Date: 2017/8/16
 * Time: 15:22
 */

namespace GatewayWorker\Lib;


use Workerman\Worker;

class business
{

    public function __construct()
    {

    }


    /**
     * 用户登陆
     * @param $data
     * @param $client_id
     * sunfan
     */
    public static function Login($data, $client_id)
    {
        $user_id = $data['user_id'];
        $app = $data['app'];//1.乘客端2.司机端
        $coordinate = $data['coordinate'];

        //通过client_id 在cmf_client_user表里查找一条信息
        $db = Db::instance('db');
        $result = $db->select('*')->from('sf_client_user')->where("client_id= '$client_id' ")->row();

        //判断是否存在相同client_id，存在直接删除
        if ($result)
        {
            $db->delete('sf_client_user')->where("client_id='$client_id'")->query();
        }

        //判断是否存在相同user_id，存在直接删除
        $result = $db->select('*')->from('sf_client_user')->where("user_id= '$user_id' ")->row();
        if ($result)
        {
            $db->delete('sf_client_user')->where("client_id='$client_id'")->query();
        }

        $insert_id = $db->insert('sf_client_user')->cols([
            'user_id'   =>  $user_id,
            'client_id' =>  $client_id,
            'coordinate'=>  $coordinate,
            'app'       =>  $app,
            'login_time'=>  date('Y-m-d H:i:s')
        ])->query();

        $_SESSION[$client_id]=$user_id;

        Gateway::sendInterfaceReturn($client_id, "Login", 1, "成功");
    }


    /**
     * 收到乘客发来坐标，并将此刻在线的司机坐标发给乘客
     * @param $data
     * @param $client
     * sunfan
     */
    public static function PassengerPosition($data, $client)
    {
        $xy = $data['xy'];
        Gateway::updateCu($client, $xy);
        Gateway::sendInterfaceReturn($client, "PassengerPosition", 1, "成功");

        //查找所有司机坐标
        $db = Db::instance('db');
        $result = $db->select('xy')->from('sf_client_user')->where("state= 1")->query();
        if (!$result)$code=1;
        foreach ($result as $k){
            if (!empty($k['xy'])){
                $driver_xy[] = $k['xy'];
            }
        }
        if (empty($driver_xy)){
            Gateway::sendInterfaceReturn($client, "PassengerPosition", 0, "附近暂时没有车辆");
        }
        $msg['driver_xys'] = $driver_xy;
        Gateway::sendInterfaceReturn($client, "PassengerPosition", 1, "成功", $msg);
    }

    /**
     * 乘客叫车
     * @param $data
     * @param $client
     * sunfan
     */
    public static function CallCar($data, $client_id)
    {
        $start_xy = $data['start_xy'];
        $end_xy = $data['end_xy'];
        $start = $data['start'];
        $end = $data['end'];

        //检查是否有未完成的订单
        $db = Db::instance('db');
        $uid = $_SESSION[$client_id];
        $order = $db->select('*')->from('sf_order')->where("passenger_id= '$uid' AND o_status< 5 ")->row();
        Worker::writeLog("查询用户id为 ".$uid." client_id为 ".$client_id." 的用户是否有未完成的订单");
        if (!empty($order)){
            Gateway::sendInterfaceReturn($client_id, "CallCar", -1, "有未完成的订单", $order['id']);
            exit();
        }

        //检查是否已在叫单
        $order_accept = $db->select('*')->from('sf_order_accept')->where("passenger_id= '$uid'")->row();
        if (!empty($order_accept)){
            Gateway::sendInterfaceReturn($client_id, "CallCar", -1, "有其他订单正在进行");
            exit();
        }



    }
}