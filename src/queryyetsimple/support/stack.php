<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use InvalidArgumentException;
use queryyetsimple\support\interfaces\stack_queue;

/**
 * 栈，后进先出
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.splstack.php
 * @version 1.0
 */
class stack extends linkedlist implements stack_queue {
    
    /**
     * 入栈
     *
     * @param mixed $mixValue            
     * @return void
     */
    public function in($mixValue) {
        $this->push ( $mixValue );
    }
    
    /**
     * 出栈
     *
     * @return mixed
     */
    public function out() {
        return $this->pop ();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \queryyetsimple\support\linkedlist::validate()
     */
    public function validate($mixValue) {
        if (! $this->checkType ( $mixValue ))
            throw new InvalidArgumentException ( __ ( '栈元素类型验证失败，允许类型为 %s', implode ( ',', $this->arrType ) ) );
    }
}