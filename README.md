## FormData

兼容thinkphp5.1、thinkphp6.0

tp6使用需在入口文件定义TP_VERSION='6.x'

如：
```
define('TP_VERSION', '6.x');
```

安装
```
composer require ke/thinkphp-utils dev-master
```

新增FormRequest，可以依赖注入使用，例子去除了命名空间，实际使用要自行添加
```
class TestRequest extends FormRequest
{
    protected $rules = [
        'id'=>'require|number',
        'str'=>'require|max:32'
    ];
}

class DemoController
{
    /**
     * 无需做其它处理，验证不通过时会抛出一个ValidateException的异常
     */
    public function post(TestRequest $request)
    {
        $form = $request->check();
        
        var_dump($form);
    }
}
```
