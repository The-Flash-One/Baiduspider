<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

// Route::get('go','index/index/go');

 Route::post('zqResources','api/index/zqResources');

 Route::get('cs','api/index/ceshi');

// Route::get('goto','index/index/goto');

// Route::get('end','index/index/zqeeb');

return [
   // 'go' => 'index/index/go',
   // 'zqResources' => 'api/index/zqResources',
   // 'cs' => 'api/index/ceshi',
  //  'goto' => 'index/index/goto',
  //  'end' => 'index/index/zqeeb',
];
