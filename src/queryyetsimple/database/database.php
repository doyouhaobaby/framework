<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\database;

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

use Exception;
use queryyetsimple\helper\helper;
use queryyetsimple\bootstrap\project;
use queryyetsimple\database\interfaces\connect;
use queryyetsimple\database\interfaces\database as interfaces_database;

/**
 * 数据库入口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class database implements interfaces_database {
    
    /**
     * 项目管理
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $objProject;
    
    /**
     * 数据库连接对象
     *
     * @var array(\queryyetsimple\database\interfaces\connect)
     */
    protected static $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $objConnect            
     * @return void
     */
    public function __construct(project $objProject) {
        $this->objProject = $objProject;
    }
    
    /**
     * 连接数据库并返回连接对象
     *
     * @param array|string $mixOption            
     * @return \queryyetsimple\database\interfaces\connect
     */
    public function connect($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objProject ['option'] ['database\\connect.' . $mixOption]) )) {
            $mixOption = [ ];
        }
        
        $strDriver = ! empty ( $mixOption ['driver'] ) ? $mixOption ['driver'] : $this->getDefaultDriver ();
        $strUnique = $this->getUnique ( $mixOption );
        
        if (isset ( static::$arrConnect [$strUnique] )) {
            return static::$arrConnect [$strUnique];
        }
        
        return static::$arrConnect [$strUnique] = $this->makeConnect ( $strDriver, $mixOption );
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objProject ['option'] ['database\default'];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objProject ['option'] ['database\default'] = $strName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return \queryyetsimple\database\interfaces\connect
     */
    protected function makeConnect($strConnect, $arrOption = []) {
        if (is_null ( $this->objProject ['option'] ['database\connect.' . $strConnect] ))
            throw new Exception ( __ ( '数据库驱动 %s 不存在', $strConnect ) );
        return $this->{'makeConnect' . ucfirst ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 mysql 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\database\mysql
     */
    protected function makeConnectMysql($arrOption = []) {
        return new mysql ( array_merge ( $this->getOption ( 'mysql' ), $arrOption ) );
    }
    
    /**
     * 取得唯一值
     *
     * @param array $arrOption            
     * @return string
     */
    protected function getUnique($arrOption) {
        return md5 ( serialize ( $arrOption ) );
    }
    
    /**
     * 读取默认数据库配置
     *
     * @param string $strConnect            
     * @return array
     */
    protected function getOption($strConnect) {
        $arrOption = $this->objProject ['option'] ['database\\'];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        
        return $this->parseOption ( array_merge ( $this->objProject ['option'] ['database\connect.' . $strConnect], $arrOption ) );
    }
    
    /**
     * 分析数据库配置参数
     *
     * @param array $arrOption            
     * @return array
     */
    protected function parseOption($arrOption) {
        $arrTemp = $arrOption;
        
        foreach ( array_keys ( $arrOption ) as $strType ) {
            if (in_array ( $strType, [ 
                    'distributed',
                    'readwrite_separate',
                    'driver',
                    'master',
                    'slave',
                    'fetch' 
            ] )) {
                if (isset ( $arrTemp [$strType] ))
                    unset ( $arrTemp [$strType] );
            } else {
                if (isset ( $arrOption [$strType] ))
                    unset ( $arrOption [$strType] );
            }
        }
        
        // 纠正数据库服务器参数
        foreach ( [ 
                'master',
                'slave' 
        ] as $strType ) {
            if (! is_array ( $arrOption [$strType] ))
                $arrOption [$strType] = [ ];
        }
        
        // 填充数据库服务器参数
        $arrOption ['master'] = array_merge ( $arrOption ['master'], $arrTemp );
        
        // 是否采用分布式服务器，非分布式关闭附属服务器
        if (! $arrOption ['distributed']) {
            $arrOption ['slave'] = [ ];
        } elseif ($arrOption ['slave']) {
            if (count ( $arrOption ['slave'] ) == count ( $arrOption ['slave'], COUNT_RECURSIVE )) {
                $arrOption ['slave'] = [ 
                        $arrOption ['slave'] 
                ];
            }
            foreach ( $arrOption ['slave'] as &$arrSlave ) {
                $arrSlave = array_merge ( $arrSlave, $arrTemp );
            }
        }
        
        // + 合并支持
        $arrOption = helper::arrayMergePlus ( $arrOption );
        
        // 返回结果
        unset ( $arrTemp );
        return $arrOption;
    }
    
    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->connect (),
                $sMethod 
        ], $arrArgs );
    }
}
