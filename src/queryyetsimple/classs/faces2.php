<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\classs;

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

use Closure;
use RuntimeException;
use BadMethodCallException;
use queryyetsimple\support\interfaces\container;

/**
 * 实现类的静态访问门面
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class faces2 {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected static $objProjectContainer = null;
    
    /**
     * 注入容器实例
     *
     * @var object
     */
    protected static $arrFacesInstance = [ ];
    
    /**
     * 获取注册容器的实例
     *
     * @param boolean $booNew            
     * @return mixed
     */
    public static function getFacesInstance() {
        $strClass = static::name ();
        if (isset ( static::$arrFacesInstance [$strClass] )) {
            return static::$arrFacesInstance [$strClass];
        }
        if (! (static::$arrFacesInstance [$strClass] = static::projectContainer ()->make ( $strClass ))) {
            static::$arrFacesInstance [$strClass] = new self ();
        }
        return static::$arrFacesInstance [$strClass];
    }
    
    /**
     * 返回服务容器
     *
     * @return \queryyetsimple\mvc\project
     */
    public static function projectContainer() {
        return static::$objProjectContainer;
    }
    
    /**
     * 设置服务容器
     *
     * @param \queryyetsimple\support\interfaces\container $objProject            
     * @return void
     */
    public static function setProjectContainer(container $objProject) {
        static::$objProjectContainer = $objProject;
    }
    
    /**
     * 缺省静态方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public static function __callStatic($sMethod, $arrArgs) {
        $objInstance = static::getFacesInstance ();
        if (! $objInstance) {
            throw new RuntimeException ( 'Can not find instance from container.' );
        }
        
        $calMethod = [ 
                $objInstance,
                $sMethod 
        ];
        if (! is_callable ( $calMethod )) {
            throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
        }
        
        return call_user_func_array ( $calMethod, $arrArgs );
    }
}