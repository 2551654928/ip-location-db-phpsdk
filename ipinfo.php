<?php
function ipinfo($ip,$ipdbname){
    //ip to long
    if (filter_var($ip, \FILTER_VALIDATE_IP,\FILTER_FLAG_IPV4)) {
        $ip_long = ip2long($ip);//int
        $iptype = "ipv4";
        
    }elseif (filter_var($ip, \FILTER_VALIDATE_IP,\FILTER_FLAG_IPV6)){
        //IPV6
        // ip2long_v6
        $ip_n = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }
    
        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($bin, 2), 10);
        } elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            // return $dec;
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }
        
        $ip_long = $dec;//string
        $iptype = "ipv6";
    }else{
        //未知IP格式
        $iptype = "noip";
        return "未知IP格式";
    }
    
    
    //get country
    //判断文件是否存在
    $ipfile = 'ip/ip-location-db-main/'.$ipdbname.'/'.$ipdbname.'-'.$iptype.'-num.csv';
    if(!file_exists($ipfile)){
        return "数据文件配置错误";
    }
    $lines = file($ipfile);
    var_dump(count($lines));
    foreach($lines as $line) {
        $ipinfo = explode(",",$line);
        $ipstart = $ipinfo[0];
        $ipend = $ipinfo[1];
        if($ip_long >= $ipstart and $ip_long <= $ipend){
            if(count($ipinfo) > 3){
                $res = "";
                for ($i = 2; $i < count($ipinfo); $i++) {
                    if($ipinfo[$i] and $ipinfo[$i] != PHP_EOL){
                     $res .= $ipinfo[$i]."-";
                    }
                }
            }else{
                $res = $ipinfo[2];
            }
            return rtrim($res,"-");
        }
    }
        return "未找到对应的IP地址";
}


$ip = $_REQUEST["ip"];
$ipdbname = "geolite2-city";
$info = trim(ipinfo($ip,$ipdbname));
var_dump($info);
