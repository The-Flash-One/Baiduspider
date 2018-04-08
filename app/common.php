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

//清楚空格等段落
function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r",'\0','\t');
    $hou=array("","","","","","","","");
    return str_replace($qian,$hou,$str);
}

//查询字符串是否大于$num字符,

function pd_char($str,$num = 20)
{
   foreach ($str as $k=>$v)
   {
       if(!empty($v)){
           foreach ($v as $key=>$val)
           {
               if(!isset($val{$num})){
                   unset($str[$k][$key]);
               }
           }
       }else{
           unset($str[$k]);
       }

   }
}


//判断对象
function object_array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

//curl数据提交

function post_date($url,$sl_data){

//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $url);//要访问的地址
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//执行结果是否被返回，0是返回，1是不返回
//curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sl_data));

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch,CURLOPT_AUTOREFERER,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$sl_data);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $return = curl_exec($ch);

    if(curl_errno($ch)){
        echo curl_error($ch);
    }

    curl_close($ch);
    return $return;

}



