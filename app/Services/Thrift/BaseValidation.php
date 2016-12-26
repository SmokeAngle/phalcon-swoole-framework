<?php
/**
 * @author chenmiao(陈淼)
 * @version 1.0.0
 */
namespace App\Services\Thrift;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;//检测字段的值是否为非空
use Phalcon\Validation\Validator\StringLength; //检测值的字符串长度
use Phalcon\Validation\Validator\Between; //检测值是否位于两个值之间
use Phalcon\Validation\Validator\Confirmation; //检测两个值是否相等
use Phalcon\Validation\Validator\Email;

class BaseValidation extends Validation
{
    public $validateRule = [];

    public function validate($data = null, $entity = null)
    {
        $this->cleanRule();
        $currentRule = array_intersect_key($this->validateRule, $data);
        if (empty($currentRule)) {
            $messageGroup = new \Phalcon\Validation\Message\Group();
            $messageGroup->appendMessage(new \Phalcon\Validation\Message('验证规则为空！'));
            return $messageGroup;
        } else {
            while ($filedName = key($currentRule)) {
                if (!is_array($currentRule[$filedName])) {
                    continue;
                }
                while ($ruleItem = current($currentRule[$filedName])) {
                      @list($className, $args) = $ruleItem;
                    if (!empty($className) && class_exists($className)) {
                        $this->add($filedName, new $className($args));
                    }
                      next($currentRule[$filedName]);
                }
                next($currentRule);
            }
            return parent::validate($data, $entity);
        }
    }
    /**
     * 清除已有规则
     */
    public function cleanRule()
    {
        $validators = $this->getValidators();
        if (!empty($validators)) {
            $this->setValidators([]);
        }
    }
}
