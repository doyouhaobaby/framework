<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mail\abstracts;

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

use Swift_Mailer;
use Swift_Transport;
use Swift_Mime_Message;
use Swift_Events_EventListener;
use queryyetsimple\classs\option;

/**
 * connect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
abstract class connect implements Swift_Transport {
    
    use option;
    
    /**
     * 返回 swift mailer
     *
     * @var \Swift_Mailer
     */
    protected $objSwiftMailer;
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->options ( $arrOption );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see Swift_Transport::isStarted()
     */
    public function isStarted() {
        return true;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see Swift_Transport::start()
     */
    public function start() {
        return true;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see Swift_Transport::stop()
     */
    public function stop() {
        return true;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see Swift_Transport::registerPlugin()
     */
    public function registerPlugin(Swift_Events_EventListener $plugin) {
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see Swift_Transport::send()
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        return $this->getSwiftMailer ()->send ( $message, $failedRecipients );
    }
    
    /**
     * 返回 swift mailer
     *
     * @return \Swift_Mailer
     */
    public function getSwiftMailer() {
        return $this->objSwiftMailer;
    }
    
    /**
     * 生成 swift mailer
     *
     * @return \Swift_Mailer
     */
    protected function swiftMailer() {
        return $this->objSwiftMailer = new Swift_Mailer ( $this->makeTransport () );
    }
}
