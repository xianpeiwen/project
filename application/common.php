<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 递归重组节点信息为多维数组
 *
 * @param array $node
 * @param number $pid
 * @author rainfer <81818832@qq.com>
 */
function node_merge(&$node, $pid = 0, $id_name = 'id', $pid_name = 'pid', $child_name = '_child')
{
    $arr = array();

    foreach ($node as $v) {
        if ($v [$pid_name] == $pid) {
            $v [$child_name] = node_merge($node, $v [$id_name], $id_name, $pid_name, $child_name);
            $arr [] = $v;
        }
    }

    return $arr;
}
/**
 * 去除多维数组中的空值
 * @author 
 * @return mixed
 * @param $arr 目标数组
 * @param array $values 去除的值  默认 去除  '',null,false,0,'0',[]
 */
function filter_array($arr, $values = ['', null, false, 0, '0',[]]) {
    foreach ($arr as $k => $v) {
        if (is_array($v) && count($v)>0) {
            $arr[$k] = filter_array($v, $values);
        }
        foreach ($values as $value) {
            if ($v === $value) {
                unset($arr[$k]);
                break;
            }
        }
    }
    return $arr;
}
//base64 转图片
function base64_upload($base64,$path) {
    
    $base64_image = str_replace(' ', '+', $base64);
    //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
        //匹配成功
        if($result[2] == 'jpeg'){
            $image_name = uniqid().'.jpg';
            //纯粹是看jpeg不爽才替换的
        }else{
            $image_name = uniqid().'.'.$result[2];
        }
        mkdirs("./images/".$path);
        $image_file = "./images/".$path."{$image_name}";
        //服务器文件存储路径
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){

            return $image_name;
            

        }else{
            return false;
        }
    }else{
        return false;
    }
}
//创建文件夹
function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
} 

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    function request_post($url = '', $param = '') {
        if (empty($url) ) {
            return false;
        }
        
        if( !empty($param)){
            foreach ( $param as $k => $v ) 
            { 
                $o.= "$k=" . urlencode( $v ). "&" ;
            }
            $post_data = substr($o,0,-1);
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }

    function get_aaa()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://apis.haoservice.com/lifeservice/car/GetSeries?&key=82c3bc59e8294abdbed8a6a5914ee469",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }
    /**
     * 计算两点地理坐标之间的距离
     * @param  Decimal $longitude1 起点经度
     * @param  Decimal $latitude1  起点纬度
     * @param  Decimal $longitude2 终点经度 
     * @param  Decimal $latitude2  终点纬度
     * @param  Int     $unit       单位 1:米 2:公里
     * @param  Int     $decimal    精度 保留小数位数
     * @return Decimal
     */
    function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){
     
        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926;
     
        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;
     
        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI /180.0;
     
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
     
        $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $distance = $distance * $EARTH_RADIUS * 1000;
     
        if($unit==2){
            $distance = $distance / 1000;
        }
     
        return round($distance, $decimal); 
    }

    /**
    * 计算某个经纬度的周围某段距离的正方形的四个点
    *
    * @param
    *            radius 地球半径 平均6371km
    * @param
    *            lng float 经度
    * @param
    *            lat float 纬度
    * @param
    *            distance float 该点所在圆的半径，该圆与此正方形内切，默认值为1千米
    * @return array 正方形的四个点的经纬度坐标
    */
    function returnSquarePoint($lng, $lat, $distance = 1, $radius = 6371)
    {
        $dlng = 2 * asin(sin($distance / (2 * $radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / $radius;
        $dlat = rad2deg($dlat);

        return array(
            'left-top' => array(
                'lat' => $lat + $dlat,
                'lng' => $lng - $dlng
            ),
            'right-top' => array(
                'lat' => $lat + $dlat,
                'lng' => $lng + $dlng
            ),
            'left-bottom' => array(
                'lat' => $lat - $dlat,
                'lng' => $lng - $dlng
            ),
            'right-bottom' => array(
                'lat' => $lat - $dlat,
                'lng' => $lng + $dlng
            )
        );
    }
    /**
     * -------------------------------------------------------------------------------------------生成二维码
     * @param  Decimal $imgPath     保存图片路径   例：orcode/admintwo/
     * @param  Decimal $app['appid']      appid
     * @param  Decimal $app['secret']      secret 
     * @return Array   $data['page']    小程序路径  ；例 ：'pages/index/index'
     ** @return Array   $data['da']    所传参数  ；例 ：'aid=1';
     */
    function addCode($imgPath,$app,$data)
    {
        $code = $imgPath.time().rand(1111,9999).'.jpg';
        $appid = $app['appid'];
        $secret = $app['secret'];

        $token = get_access_token($appid,$secret);
        
        $shop_id = $data['da'];
        $page = $data['page'];
        $path = $code;
      
        get_qrcode($token,$shop_id,$page,$path,$imgPath);
        
        return $code;
    }

    //微信小程序获取access_token
    function get_access_token($appid,$secret)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        return $data = curl_get($url);
    }
    //模拟GET
    function curl_get($url) 
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $data;
    }
    /** 
    * @name thum  获取微信小程序二维码 
    * @param  sting  $access_token  微信小程序的access_token 
    * @param  sting   $scene        所传的参数
    * @param  sting   $page         需要跳转的小程序路径
    * @param  sting  $path          二维码保存的路径
    * @return  无 
    */
    function get_qrcode($access_token,$scene,$page,$path,$imgPath) 
    {

        // header('content-type:image/gif');
        //header('content-type:image/png');格式自选，不同格式貌似加载速度略有不同，想加载更快可选择jpg
        header('content-type:image/jpg');
        $data = array();
        $data['page'] = $page;
        $data['scene'] = $scene;
        $data = json_encode($data);
        $access = json_decode($access_token,true);
        $access_token= $access['access_token'];
        
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
   
        $da = api_notice_increment($url,$data);
        mkdirs('./images/'.$imgPath);
        file_put_contents('./images/'.$path, $da); 
    }
    //秒转换
    function secToTime($times,$t=0){ 
        if( $t == 0){

            $result = '00:00:00';  
        }else{
            $result['h'] = '00';
            $result['m'] = '00';
            $result['s'] = '00';
        }
        if ($times>0) {  
            $hour = floor($times/3600);  
            $minute = floor(($times-3600 * $hour)/60);  
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);  
            $hour < 10?$hour = '0'.$hour:$hour;
            $minute < 10?$minute = '0'.$minute:$minute;
            $second < 10?$second = '0'.$second:$second;
            if( $t == 0){
                $result = $hour.'小时'.$minute.'分'.$second.'秒';  
            }else{

                $result['h'] = $hour;
                $result['m'] = $minute;
                $result['s'] = $second;
            }   
        }  
        return $result;  
    }
    function api_notice_increment($url, $data)
    {

        $curl = curl_init(); // 启动一个CURL会话      
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作 
        if (curl_errno($curl)) { 
            echo 'Errno'.curl_error($curl); 
        }
        curl_close($curl); // 关键CURL会话 
        return $tmpInfo; // 返回数据
    }
    function numberFormat($number) {
        if ($number >= 10000) {
            return number_format($number/10000,2) . '万';
        }
        return $number;
    }

//随机数n位
function getRandChar($length)
{
    $str = null;
    $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

function pp($data,$option=1){
    if ($option==1){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }else{
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}

