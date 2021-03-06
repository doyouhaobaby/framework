<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\bootstrap;

/**
 * 启动程序
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
class bootstrap
{
    
    /**
     * 父控制器
     *
     * @var queryyetsimple\bootstrap\project
     */
    protected $objProject;
    
    /**
     * 项目配置
     *
     * @var array
     */
    protected $arrOption = [];
    
    /**
     * 执行事件流程
     *
     * @var array
     */
    protected $arrEvent = [
        'check', 
        'registerRuntime', 
        'initProject', 
        'router', 
        'runApp'
    ];
    
    /**
     * 构造函数
     *
     * @param queryyetsimple\bootstrap\project $objProject
     * @param array $arrOption
     * @return void
     */
    public function __construct(project $objProject = null, $arrOption = [])
    {
        $this->objProject = $objProject;
        $this->arrOption = $arrOption;
    }
    
    /**
     * 执行初始化事件
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->arrEvent as $strEvent) {
            $this->{$strEvent}();
        }
    }
    
    /**
     * 项目初始化验证
     *
     * @return void
     */
    protected function check()
    {
        if (version_compare(PHP_VERSION, '7.1.0', '<')) {
            die('PHP 7.1.0 OR Higher');
        }
        
        if (env('queryphp_version')) {
            return;
        }
    }
    
    /**
     * QueryPHP 系统错误处理
     *
     * @return void
     */
    protected function registerRuntime()
    {
        if (PHP_SAPI == 'cli') {
            return;
        }

        register_shutdown_function([
            'queryyetsimple\bootstrap\runtime\runtime', 
            'shutdownHandle'
        ]);
        
        set_error_handler([
            'queryyetsimple\bootstrap\runtime\runtime', 
            'errorHandle'
        ]);
        
        set_exception_handler([
            'queryyetsimple\bootstrap\runtime\runtime', 
            'exceptionHandle'
        ]);
    }
    
    /**
     * 初始化项目
     *
     * @return void
     */
    protected function initProject()
    {
        // 注册公共组件命名空间
        $this->objProject['psr4']->import('common', $this->objProject->pathCommon());
        
        // 载入 project 引导文件
        if (is_file(($strBootstrap = $this->objProject->pathCommon() . '/bootstrap.php'))) {
            require_once $strBootstrap;
        }
    }
    
    /**
     * 执行路由请求
     *
     * @return void
     */
    protected function router()
    {
        // 运行笑脸初始化应用
        $this->objProject->make(application::class, [
            application::INIT_APP, 
            $this->arrOption
        ])->bootstrap()->namespaces();
        
        // 完成路由请求
        $this->objProject->router->run();
    }
    
    /**
     * 执行应用
     *
     * @return void
     */
    protected function runApp()
    {
        // 创建 & 注册
        $objApp = $this->objProject->make(application::class)->bootstrap($this->objProject->router->app());
        
        // 运行应用
        $objApp->run();
    }
}
