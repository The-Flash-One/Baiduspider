<?php
/**
 * Created by PhpStorm.
 * User: zxq
 * Date: 2018/3/31
 * Time: 14:21
 */
namespace app\index\controller;

use think\Controller;
use QL\QueryList;
use QL\Ext\CurlMulti;

class Ceshi extends Controller
{
    public function index(){
           return $this->fetch();
    }
    public function readContent(){
        $title = input('post.');
        print_r($title);
    }
    public function hello()
    {

// 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
        $ql = QueryList::get('http://news.ifeng.com/listpage/11502/0/1/rtlist.shtml')->rules([
            'link' => array('.newsList>ul>li>a', 'href'),
            'title'=> array('.newsList>ul>li>a', 'text'),
        ]);
        $data = $ql->encoding('UTF-8')->removeHead()->query()->getData();

        $con = $data->all();

        print_r($con);

    }

    public  function  tre(){
        $cql = QueryList::get('http://news.ifeng.com/a/20180402/57256673_0.shtml');
        $content = $cql->encoding('UTF-8')->removeHead()->find('#main_content')->texts();
        $cjcontent = $content->all();
        $cql->destruct();
        print_r($cjcontent);
    }
}