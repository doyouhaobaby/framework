<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
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
 * 日志默认配置文件
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
return [ 
        
        /**
         * ---------------------------------------------------------------
         * 默认日志驱动
         * ---------------------------------------------------------------
         *
         * 系统为所有日志提供了统一的接口，在使用上拥有一致性
         */
        'default' => env ( 'log_driver', 'file' ),
        
        /**
         * ---------------------------------------------------------------
         * 是否启用日志
         * ---------------------------------------------------------------
         *
         * 默认记录日志，记录日志会消耗服务器资源
         */
        'enabled' => true,
        
        /**
         * ---------------------------------------------------------------
         * 允许记录的日志级别
         * ---------------------------------------------------------------
         *
         * 随意自定义,其中 debug、info、notice、warning、error、critical、alert 和 emergency 为系统内部使用
         */
        '+level' => [ 
                'debug',
                'info',
                'notice',
                'warning',
                'error',
                'critical',
                'alert',
                'emergency' 
        ],
        
        /**
         * ---------------------------------------------------------------
         * 日志时间格式化
         * ---------------------------------------------------------------
         *
         * 每条日志信息开头的时间信息
         */
        'time_format' => '[Y-m-d H:i]',
        
        /**
         * ---------------------------------------------------------------
         * 日志连接参数
         * ---------------------------------------------------------------
         *
         * 这里为所有的日志的连接参数，每一种不同的驱动拥有不同的配置
         * 虽然有不同的驱动，但是在日志使用上却有着一致性
         */
        '+connect' => [ 
                
                '+file' => [
                        // driver
                        'driver' => 'file',
                        
                        // 日志文件名时间格式化
                        'name' => 'Y-m-d H',
                        
                        // 日志文件大小限制,单位为字节 byte
                        'size' => 2097152,
                        
                        // 默认的日志路径，如果没有则设置为 \queryyetsimple\bootstrap\project::bootstrap ()->path_cache_log
                        'path' => project ( 'path_cache_log' ) 
                ] 
        ] 
];
