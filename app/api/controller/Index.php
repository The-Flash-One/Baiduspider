<?php
/**
 * Created by PhpStorm.
 * User: zxq
 * Date: 2018/4/2
 * Time: 16:59
 */

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    protected static $jnum = 0;
    protected static $zqresult = array();
    protected static $juzi = '';


    function index()
    {

        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> 新闻源 V1.0<br/><span style="font-size:30px">新闻源接口 - 你值得信赖的API框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';

    }

    function zqResources()
    {

        if(request()->isPost()){
           $id = input('post.id');
           $new = Db::name('apiurl')->field('username,pwd')->where('id',1)->find();

           $username = input('post.username');
           $password = md5(input('post.password'));
          if($username == $new['username']  && $password == $new['pwd']){

              $num = input('post.num');
              if (empty($num)) {
                  $num = 10;
              }
              $result = self::$zqresult;
              //获取句子
              $result['content'] = $this->dzContent($num);
              //获取标题
              $result['title'] = $this->dzTitle($num);
              $result['images'] =$this->getImagezx($num);

              $result = json_encode($result,true);
              exit($result);
          }else
              {
                  echo '接口验证失败';
              }

        }else{
            echo '测试数据';
        }



    }

    public function dzContent($num)
    {
        $content = Db::name('content')->field('id,content')->where('recycle', 1)->order('id Desc')->limit(1500)->select();

        // 随机合成文章
        $turejz = shuffle($content);

        if (!$turejz) {
            echo '读取内容数据库失败';
        }
        $rnum = self::$jnum;
        $result = self::$zqresult;
        $imagesnum = self::$jnum;
        //获取图片
        $images = $this->getRandImages();

        for ($i = 0; $i < $num; $i++) {
               $randnum = mt_rand(8, 15);
               $recont = '';
                for ($j = 0; $j < $randnum; $j++) {
                if($j == 0){
                    $recont .= "<p style='text-aglin:center;'><img src='".$images[$imagesnum]['listurl']."'  alt='\$标题\$' rel='nofllow'></p>";
                    $recont .= '<p>' . $content[$rnum]['content'] . $content[$rnum + 1]['content'] . '</p>';
                }elseif($j == ceil($randnum/2)){
                    $recont .= "<p style='text-aglin:center;'><img src='".$images[$imagesnum + 1]['listurl']."'  alt='\$标题\$' rel='nofllow'></p>";
                    $recont .= '<p>' . $content[$rnum]['content'] . $content[$rnum + 1]['content'] . '</p>';
                }else{
                    $recont .= '<p>' . $content[$rnum]['content'] . $content[$rnum + 1]['content'] . '</p>';
                }
                $rnum = $rnum + $j +2;

            }
            $imagesnum = $imagesnum + 2;
            $result[$i] = $recont;
        }
        return $result;
    }

    public function dzTitle($num)
    {
        $title = Db::name('title')->field('id,title')->where('recycle', 1)->order('id Desc')->limit(1500)->select();
        $turet = shuffle($title);
        if (!$turet) {
            echo '读取标题数据库失败';
        }
        $result = self::$zqresult;
        for ($i = 0; $i < $num; $i++) {
            $result[$i] = $title[$i]['title'];
        }
        return $result;
    }

    public function getRandImages(){
        $num = 50;    //需要抽取的默认条数
        $table = 'images';    //需要抽取的数据表
        $countcus = Db::name($table)->count();    //获取总记录数
        $min = Db::name($table)->min('id');    //统计某个字段最小数据
        if($countcus < $num){$num = $countcus;}
        $i = 1;
        $flag = 0;
        $ary = array();
        while($i<=$num){
            $rundnum = rand($min, $countcus);//抽取随机数
            if($flag != $rundnum){
                //过滤重复
                if(!in_array($rundnum,$ary)){
                    $ary[] = $rundnum;
                    $flag = $rundnum;
                }else{
                    $i--;
                }
                $i++;
            }
        }
        $list = Db::name($table)->field('id,listurl')->where('id','in',$ary,'or')->select();
       return $list;
    }
    //抽取制定数量的图片
    public function getImagezx($num){
        $result = self::$zqresult;
        $j = self::$jnum;
        $num = 2*$num;    //需要抽取的默认条数
        $table = 'images';    //需要抽取的数据表
        $countcus = Db::name($table)->count();    //获取总记录数
        $min = Db::name($table)->min('id');    //统计某个字段最小数据
        if($countcus < $num){$num = $countcus;}
        $i = 1;
        $flag = 0;
        $ary = array();
        while($i<=$num){
            $rundnum = rand($min, $countcus);//抽取随机数
            if($flag != $rundnum){
                //过滤重复
                if(!in_array($rundnum,$ary)){
                    $ary[] = $rundnum;
                    $flag = $rundnum;
                }else{
                    $i--;
                }
                $i++;
            }
        }
        $list = Db::name($table)->field('id,listurl')->where('id','in',$ary,'or')->select();

        for($i = 0;$i<$num;$i = $i+2)
        {
            $result[$j][0] = $list[$i]['listurl'];
            $result[$j][1] = $list[$i+1]['listurl'];
            $j++;
        }
      return $result;
    }
    public  function ceshi()
    {
        $date = [
            'id' => '1',
            'username' => 'admin',
            'password' => 'admin456',

        ];

        $content =  post_date('http://127.0.0.1/zqResources',$date);
    //    $content = json_decode($content);
    //    $content = object_array($content);
        print_r($content);

    }

    public function add()
    {
        exit;
        set_time_limit(0);
        $titleold = file_get_contents();

        $titlenews = mb_convert_encoding($titleold, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        $title = strip_tags($titlenews);

        $contents = explode("\n", $title);//explode()函数以","为标识符进行拆分
        foreach ($contents as $k => $v) {
            $val = trimall($v);
            if (empty($val)) {
                unset($contents[$k]);
            } else {
                $date[$k]['listurl'] = $val;
            }

        }

        if (!isset($contents)) {
            $this->error('数据导入失败', 'api/index');
        }


        $result = Db::name('images')->limit(10000,20000)->insertAll($date);

        if ($result) {
            $this->success('数据导入成功', 'api/index');
        } else {
            $this->error('数据导入失败', 'api/index');
        }


    }

}