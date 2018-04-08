<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use QL\QueryList;
use QL\Ext\CurlMulti;
use think\Request;
use think\View;

class Index extends Controller
{

    protected static $dbresult = array();

    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> 新闻源 V1.0<br/><span style="font-size:30px">新闻源接口 - 你值得信赖的API数据接口</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }
    public function go()
    {
        $this->success('数据真正抓取', url('Index/goTo', ['tonum' => 0]));
    }

    public function goTo()
    {
        set_time_limit(0);
        $gonum = $this->request->param('tonum');
        $redb = Db::name('website')->where('recycle', 1)->order('id asc')->select();
        $endnum = count($redb);

        if ($gonum < $endnum) {
            //域名
            $zqweb = $redb[$gonum]['web'];
            //采集url列表
            $zqlist = $redb[$gonum]['listurl'];
            //采集标题
            $zqtitle = $redb[$gonum]['listtitle'];
            //采集内容
            $zqcontent = $redb[$gonum]['listcontent'];
            //url补偿
            $zqurl = $redb[$gonum]['urltype'];

            // 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
            $ql = QueryList::get($zqweb)->rules([
                'title' => array($zqtitle, 'text'),
                'link' => array($zqlist, 'href')
            ]);
            $data = $ql->encoding('UTF-8')->removeHead()->query()->getData();
            $reurl = $data->all();
            $ql->destruct();
            foreach ($reurl as $key => $value) {
                $oldtitle[$key] = $value['title'];
                $oldlink[$key] = $value['link'];
            }

            $titile = $this->readTitle($oldtitle);
            if ($titile) {
                echo '标题数据导入成功<br/>';
            }
            //url进行处理
            $newreurl = $this->readUrl($oldlink, $zqurl);

            $oldcontent = $this->zqGcon($newreurl, $zqcontent);

            $content = $this->readStr($oldcontent);

            if ($content) {
                echo '内容数据数据导入成功<br/>';
            }
            $gonum++;
            $this->success('数据写入成功，正在抓取新数据，请稍等片刻', url('Index/goTo', ['tonum' => $gonum]));

        } else {
            $this->success('数据采集完成', url('Index/zqEeb'));
        }

    }

    public function zqGcon($result, $zqcontent)
    {
        foreach ($result as $k => $v) {
            $cql = QueryList::get($v);
            $content = $cql->encoding('UTF-8')->removeHead()->find($zqcontent)->texts();
            $cjcontent = $content->all();
            $cql->destruct();
            $result[$k] = $cjcontent;
        }
        return $result;
    }


    public function zqEeb()
    {
        echo '内容采集完成';
    }

//文章标题数据
    public function readTitle($title)
    {
        $db = self::$dbresult;

        foreach ($title as $k => $v) {
            if (isset($v{15})) {
                $newt = trimall($v);
                $db[$k]['title'] = $newt;
            }
        }
        $result = Db::name('title')->data($db)->insertAll();


    }

//文章内容数据
    public function readStr($str, $num = 80)
    {

        foreach ($str as $k => $v) {
            $db = array();
            if (!empty($v)) {
                foreach ($v as $key => $val) {
                    if (isset($val{$num})) {
                        $val = trimall($val);
                        $db[$key]['content'] = $val;
                    }
                }
                $result = Db::name('content')->data($db)->limit(100)->insertAll();
            }
        }
        return $result[0];
    }

    //url补偿
    public function readUrl($url, $urltype)
    {

        foreach ($url as $key => $value) {
            if (!empty($urltype)) {
                $newurl[$key] = $urltype . $value;
            } else {
                $newurl[$key] = $value;
            }

        }
        return $newurl;
    }
}
