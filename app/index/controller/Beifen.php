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
        $this->success('数据开始抓取', url('Index/goTo', ['tonum' => 0]));
    }


    public function go($url)
    {
        foreach ($url as $k => $v) {
            if (!empty($v['link'])) {
                $newurl[$k] = $v['link'];
            }
        };

        $ql = QueryList::use(CurlMulti::class);
        $ql->curlMulti($newurl)->success(function (QueryList $ql, CurlMulti $curl, $r) {   // 每个任务成功完成调用此回调
            $data = $ql->rules([
                'content' => array('p', 'text', '', function ($content) {
                    $qian = array(" ", "　", "\t", "\n", "\r", '\0', '\t');
                    $hou = array("", "", "", "", "", "", "", "");
                    return str_replace($qian, $hou, $content);
                })
            ])->encoding('UTF-8', 'GB2312')->removeHead()->range('.left_zw')->query()->getData();
            $con = $data->all();
            print_r($con);
        })
            // 每个任务失败回调
            ->error(function ($errorInfo, CurlMulti $curl) {
                echo "Current url:{$errorInfo['info']['url']} \r\n";
                print_r($errorInfo['error']);
            })
            ->start([
                // 最大并发数
                'maxThread' => 1000,
                // 错误重试次数
                'maxTry' => 3,
                'opt' => [
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 1,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                ],
            ]);

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
            foreach ($reurl as $key=>$value)
            {
                $oldtitle[$key] = $value['title'];
                $oldlink[$key] = $value['link'];
            }

            $title = json_encode($oldtitle);
            //url进行处理
            $newreurl = $this->readUrl($oldlink,$zqurl);

            $content = $this->zqGcon($newreurl,$zqcontent);

            $content = json_encode($content);

            return view('goto',['title'=>$title,'content'=>$content,'web'=>$zqweb,'tonum'=>$gonum]);

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
            $result[$k] =  $cjcontent;
        }
        return $result;
    }

    public function readContent() //数据处理
    {

        $gonum = $this->request->param('tonum');
        //内容数据处理
        $oldtitle = input('post.title');
        $newtitle =json_decode($oldtitle);
        $tonum = $this->request->param('tonum');
        $titile = $this->readTitle($newtitle);

        $oldcon = input('post.content');
        $newcon = json_decode($oldcon);

        
        $content = $this->readStr($newcon);


        if($titile){
            echo "标题数据写入成功";
        }
        if($content){
            echo  '内容数据写入成功';
        }

        $this->success('数据写入成功', url('Index/zqEeb',['tonum' => $gonum]));

    }

    public function zqEeb()
    {
        echo '内容采集完成';
    }
//文章标题数据
    public function readTitle($title)
    {
        $db = self::$dbresult;

        foreach ($title as $k=>$v)
        {
              if(isset($v{15})){
                  $newt = trimall($v);
                  $db[$k]['title'] = $newt;
              }
        }
        $result =  Db::name('title')->data($db)->insertAll();


    }

//文章内容数据
    public function readStr($str,$num = 80)
    {

        foreach ($str as $k=>$v)
        {
            $db = array();
            if(!empty($v)){
                foreach ($v as $key=>$val)
                {
                    if(isset($val{$num})){
                        $val = trimall($val);
                        $db[$key]['content'] = $val;
                    }
                }
                $result =  Db::name('content')->data($db)->limit(100)->insertAll();
            }
        }
       return $result[0];
    }

 //url补偿
    public function readUrl($url,$urltype){

              foreach ($url as $key=>$value)
              {
                  if(!empty($urltype)){
                      $newurl[$key]= $urltype.$value;
                  }else{
                      $newurl[$key]= $value;
                  }

              }
              return  $newurl;
    }


}
