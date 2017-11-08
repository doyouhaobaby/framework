<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session;

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

use queryyetsimple\support\manager as support_manager;

/**
 * manager 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class manager extends support_manager {
    
    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace() {
        return 'session';
    }
    
    /**
     * 创建连接对象
     *
     * @param object $objConnect            
     * @return object
     */
    protected function createConnect($objConnect) {
        return new session ( $objConnect, $this->getOptionCommon () );
    }
    
    /**
     * 创建 cookie 缓存
     *
     * @param array $arrOption            
     * @return null
     */
    protected function makeConnectCookie($arrOption = []) {
        return null;
    }
    
    /**
     * 创建 memcache 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\session\memcache
     */
    protected function makeConnectMemcache($arrOption = []) {
        return new memcache ( array_merge ( $this->getOption ( 'memcache', $arrOption ) ) );
    }
    
    /**
     * 创建 redis 缓存
     *
     * @param array $arrOption            
     * @return \queryyetsimple\session\redis
     */
    protected function makeConnectRedis($arrOption = []) {
        return new redis ( array_merge ( $this->getOption ( 'redis', $arrOption ) ) );
    }
    
    /**
     * 读取连接配置
     *
     * @param string $strConnect            
     * @return array
     */
    protected function getOptionConnect($strConnect) {
        return $this->optionFilterNull ( parent::getOptionConnect ( $strConnect ) );
    }
}