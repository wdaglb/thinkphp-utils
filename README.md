## FormData

兼容thinkphp5.0、thinkphp5.1、thinkphp6.0

tp6使用需在入口文件定义TP_VERSION='6.x'

tp5.0使用需在入口文件定义TP_VERSION='5.0'

如：
```
define('TP_VERSION', '6.x');
```

安装
```
composer require ke/thinkphp-utils dev-master
```

定义请求类
```
<?php

namespace app\request\ArticleRequest;

use ke\utils\FormRequest;

class ArticleRequest extends FormRequest
{
    public $rules = [
        'cate_id' => 'require|number',
        'name' => 'require|number',
        'title' => 'require|number',
    ];
}
```

使用请求类
```
$form = new ArticleRequest();
$params = $form->check();
var_dump($params); // 只会打印cate_id，name，title这3个字段
```
