<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use QL\QueryList;
use QL\Ext\CurlMulti;
use think\Request;

class Index extends Controller
{

    protected $num,$bdresult;

    public function hello()
    {

// 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
        $ql = QueryList::get('https://www.chinanews.com/scroll-news/news1.html')->rules([
            'link' => array('.dd_bt>a', 'href')
        ]);
        $data = $ql->encoding('UTF-8', 'GB2312')->removeHead()->query(function ($item) {
            $item['link'] = 'https:' . $item['link'];
            $result = strpos($item['link'], 'shipin');
            if ($result) {
                $item['link'] = 0;
                return $item;
            }
            return $item;

        })->getData();

        $con = $data->all();

        $content = $this->go($con);

        print_r($content);

    }

    public function add()
    {
        $curl = 'https://www.chinanews.com/gn/2018/03-29/8479059.shtml';
// 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
        $ql = QueryList::get('https://www.chinanews.com/sh/2018/03-29/8479114.shtml')->rules([

            'content' => array('p', 'text', '', function ($content) {
                $qian = array(" ", "　", "\t", "\n", "\r", '\0', '\t');
                $hou = array("", "", "", "", "", "", "", "");
                return str_replace($qian, $hou, $content);
            })
        ]);
        $data = $ql->encoding('UTF-8', 'GB2312')->removeHead()->range('.left_zw')->query()->getData();
        $content = $data->all();

        print_r($content);
    }

    public function two()
    {
        // 采集该页面文章列表中所有[文章]的超链接和超链接文本内容
        $ql = QueryList::get('https://www.chinanews.com/scroll-news/news1.html')->rules([
            'title' => array('.dd_bt', 'text'),
            'link' => array('.dd_bt>a', 'href')
        ]);
        $data = $ql->encoding('UTF-8', 'GB2312')->removeHead()->query(function ($item) {
            $item['link'] = 'https:' . $item['link'];
            return $item;
        })->getData(function ($item) {

            $cql = QueryList::get($item['link'])->rules([
                'content' => array('p', 'text', '', function ($content) {
                    $qian = array(" ", "　", "\t", "\n", "\r", '\0', '\t');
                    $hou = array("", "", "", "", "", "", "", "");
                    return str_replace($qian, $hou, $content);
                })
            ]);
            $item['link'] = $cql->encoding('UTF-8', 'GB2312')->removeHead()->range('.left_zw')->query()->getData();
            $cql->destruct();
            return $item;

        });
        $ql->destruct();
        $content = $data->all();
        print_r($content);

    }

    public  function  tre(){
        $cql = QueryList::get('https//www.chinanews.com/hr/2018/04-02/8481434.shtml');
        $content = $cql->find('.left_zw>p')->texts();
        $cjcontent = $content->all();
        $cql->destruct();
        echo $cjcontent;
    }
}
