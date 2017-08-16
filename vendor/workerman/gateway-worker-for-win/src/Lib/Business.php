<?php
/**
 * Created by PhpStorm.
 * User: sunfan
 * Date: 2017/8/16
 * Time: 15:22
 */

namespace GatewayWorker\Lib;


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

        Gateway::sendInterfaceReturn($client_id, "Login", 1, "成功");
    }

    public function PassengerPosition($data, $client){
        $xy = $data['xy'];
    }
}