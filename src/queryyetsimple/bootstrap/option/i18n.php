<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
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

/**
 * 国际化默认配置文件
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [ 
        
        /**
         * ---------------------------------------------------------------
         * 是否开启语言包
         * ---------------------------------------------------------------
         *
         * 如果你的项目需要多语言请设置为 true，否则设置为 false
         */
        'on' => true,
        
        /**
         * ---------------------------------------------------------------
         * 语言切换 cookie key 是否包含 app_name
         * ---------------------------------------------------------------
         *
         * 如果你需要不同的 app 实现不同的语言切换，可以设置为 true
         * 否则所有的 app 的语言切换都采用相同的 key
         */
        'cookie_app' => false,
        
        /**
         * ---------------------------------------------------------------
         * 是否允许切换语言包
         * ---------------------------------------------------------------
         *
         * 基于 cookie 实现语言切换
         */
        'switch' => true,
        
        /**
         * ---------------------------------------------------------------
         * 当前语言环境
         * ---------------------------------------------------------------
         *
         * 根据面向的客户设置当前的软件的语言
         */
        'default' => 'zh-cn',
        
        /**
         * ---------------------------------------------------------------
         * 当前开发语言环境
         * ---------------------------------------------------------------
         *
         * 如果为当前开发语言则不载入语言包直接返回
         */
        'develop' => 'zh-cn',
        
        /**
         * ---------------------------------------------------------------
         * 自动侦测语言
         * ---------------------------------------------------------------
         *
         * 系统会根据当前运行上下文自动分析需要的语言
         */
        'auto_accept' => true 
]; 
