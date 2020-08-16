<?php
/**
 * +----------------------------------------------------------------------
 * | Name: keAdmin
 * | Author King east To 1207877378@qq.com
 * +----------------------------------------------------------------------
 */


namespace ke\utils;


use think\App;
use think\Container;

class FormData
{
    /**
     * @var App
     */
    private $app;


    private $data;


    private $validate;


    private $_rules = [];

    private $ext_keys = [];

    private $fields = [];


    public function __construct($rule, $scene = null)
    {
        $this->app = Container::get('app');
        if (is_string($rule)) {
            $n = new $rule;
            $this->_rules = $n->rule;
            $this->_rules = $this->ruleFilter($this->_rules);
            $this->validate = $this->app->validate($rule);
        } else {
            $this->_rules = $rule;
            $this->_rules = $this->ruleFilter($this->_rules);
            $this->validate = $this->app->validate();
            $this->validate->rule($this->_rules);
        }

        $this->data = $this->app->request->param();
        if (!is_null($scene)) {
            $this->scene($scene);
        }
    }


    private function ruleFilter($rule)
    {
        $arr = [];
        foreach ($rule as $str=>$value) {
            if (is_int($str)) {
                if (strpos($value, '|') === false) {
                    $this->fields[] = $value;
                } else {
                    list($l, $r) = explode('|', $value);
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
     * 设置验证场景
     * @param string $name
     * @return FormData
     */
    public function scene($name)
    {
        $this->validate->scene($name);
        return $this;
    }


    /**
     * 设置提示信息
     * @param array $message
     * @return FormData
     */
    public function message($message)
    {
        $this->validate->message($message);
        return $this;
    }


    /**
     * 验证
     * @param array $data
     * @return bool
     */
    public function check($data = null)
    {
        if (is_null($data)) {
            $this->data = $this->app->request->param();
        } else {
            $this->data = $data;
        }
        if ($this->validate->check($this->data)) {
            return true;
        }
        return false;
    }


    /**
     * @return string
     */
    public function getError()
    {
        return $this->validate->getError();
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $postData = [];
        foreach ($this->fields as $f) {
            if (isset($this->data[$f])) {
                $postData[$f] = $this->data[$f];
            }
        }
        foreach ($this->ext_keys as $key) {
            $postData[$key] = $this->data[$key];
        }
        return $postData;
    }

    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name] ?? '';
    }


    public function __set($name, $value)
    {
        if (array_search($name, $this->ext_keys) === false) {
            $this->ext_keys[] = $name;
        }
        $this->data[$name] = $value;
    }


    public function __isset($name)
    {
        return isset($this->data[$name]);
    }


    public function __unset($name)
    {
        unset($this->data[$name]);
    }

}
