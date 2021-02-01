<?php
/**
 * +----------------------------------------------------------------------
 * | Name: ThinkPHP-Utils
 * | Author King east To 1207877378@qq.com
 * +----------------------------------------------------------------------
 */


namespace ke\utils;


use think\App;
use think\Container;
use think\exception\ValidateException;
use think\Validate;

class FormRequest
{
    /**
     * @var App
     */
    protected $app;


    protected $data;


    private $validate;


    protected $rules = [];


    private $ext_keys = [];


    private $fields = [];


    protected $defaults = [];


    private $alias = [];

    protected $currentScene = '';

    /**
     * 验证场景定义
     * @var array
     */
    protected $scene = [];


    public function __construct()
    {
        if (defined('TP_VERSION') && TP_VERSION == '6.x') {
            $this->app = Container::pull('app');
            $this->validate = $this->app->validate;
        } else {
            $this->app = Container::get('app');
            $this->validate = $this->app->validate();
        }

        $this->data = $this->app->request->param();
        $this->rules = $this->ruleFilter($this->rules);
        $this->validate->rule(get_class($this));
    }


    private function ruleFilter($rule)
    {
        $arr = [];
        foreach ($rule as $str=>$value) {
            if (is_int($str)) {
                if (strpos($value, '|') === false) {
                    $this->fields[] = $value;
                } else {
                    [$l, $r] = explode('|', $value);
                    $this->fields[] = $l;
                }
            } else {
                if (strpos($str, '|') === false) {
                    $this->fields[] = $str;
                } else {
                    $this->fields[] = explode('|', $str)[0];
                }
                $arr[$str] = $value;
            }
        }

        return $arr;
    }


    /**
     * 扩展可选无规则字段
     * @param array $fields
     * @return $this
     */
    public function extend(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }


    /**
     * 设置验证场景
     * @param string $name
     * @return $this
     */
    public function scene($name)
    {
        $this->currentScene = $name;
        return $this;
    }


    /**
     * 设置提示信息
     * @param array $message
     * @return $this
     */
    public function message($message)
    {
        $this->validate->message($message);
        return $this;
    }

    /**
     * 指定需要验证的字段列表
     * @access public
     * @param  array $fields  字段名
     * @return $this
     */
    public function only($fields)
    {
        $this->validate->only($fields);
        return $this;
    }

    /**
     * 移除某个字段的验证规则
     * @access public
     * @param  string|array  $field  字段名
     * @param  mixed         $rule   验证规则 null 移除所有规则
     * @return $this
     */
    public function remove($field, $rule = null)
    {
        $this->validate->remove($field, $rule);
        return $this;
    }

    /**
     * 追加某个字段的验证规则
     * @access public
     * @param  string|array  $field  字段名
     * @param  mixed         $rule   验证规则
     * @return $this
     */
    public function append($field, $rule = null)
    {
        $this->validate->append($field, $rule);
        return $this;
    }


    /**
     * 验证
     * @param array $data
     * @return array
     */
    public function check($data = null)
    {
        if (is_null($data)) {
            $this->data = $this->app->request->param();
        } else {
            $this->data = $data;
        }

        if (!$this->scene) {
            // 自动切换至方法场景
            $this->currentScene = $this->app->request->action();

            $this->validate->scene($this->currentScene);
        }

        if ($this->validate->check($this->data)) {
            return $this->toArray();
        }
        throw new ValidateException($this->validate->getError());
    }


    public function getError()
    {
        return $this->validate->getError();
    }


    /**
     * 设置key的默认值,如果key不在rules列表里则不生效
     * @param array $data
     * @return $this
     */
    public function setDefault(array $data)
    {
        $this->defaults = array_merge($this->defaults, $data);
        return $this;
    }


    /**
     * 设置键别名
     * @param array $data
     * @return $this
     */
    public function setAlias(array $data)
    {
        $this->alias = $data;
        return $this;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $postData = array_merge($this->data, $this->defaults);
        foreach ($this->fields as $f) {
            if (isset($this->data[$f])) {
                $postData[$f] = $this->data[$f];
            }
        }
        foreach ($this->ext_keys as $key) {
            if (isset($this->data[$key])) {
                $postData[$key] = $this->data[$key];
            }
        }
        // 别名转换
        foreach ($this->alias as $from=>$to) {
            if (isset($postData[$from])) {
                $postData[$to] = $postData[$from];
                unset($postData[$from]);
            }
        }
        return $postData;
    }


    public function has($name)
    {
        return isset($this->data[$name]);
    }

}
