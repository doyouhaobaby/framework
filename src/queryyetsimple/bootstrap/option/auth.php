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
 * auth 默认配置文件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
return [ 
        
        /*
         * |--------------------------------------------------------------------------
         * | Authentication Defaults
         * |--------------------------------------------------------------------------
         * |
         * | This option controls the default authentication "guard" and password
         * | reset options for your application. You may change these defaults
         * | as required, but they're a perfect start for most applications.
         * |
         */
        
        'default' => 'api',
        
        /**
         * ---------------------------------------------------------------
         * 默认日志驱动
         * ---------------------------------------------------------------
         *
         * 系统为所有日志提供了统一的接口，在使用上拥有一致性
         */
        'web_default' => 'session',
        
        'api_default' => 'token',
        
        /**
         * ---------------------------------------------------------------
         * 日志连接参数
         * ---------------------------------------------------------------
         *
         * 这里为所有的日志的连接参数，每一种不同的驱动拥有不同的配置
         * 虽然有不同的驱动，但是在日志使用上却有着一致性
         */
        '+connect' => [ 
                
                '+session' => [
                        // driver
                        'driver' => 'session',
                        
                        // 模型
                        'model' => common\domain\model\common_user::class,
                        
                        'prefix' => 'q_',
                        'cookie' => 'auth',
                        'session' => 'auth',
                        'field' => 'id,name,nikename,email,mobile' 
                ],
                
                  '+token' => [
                        // driver
                        'driver' => 'token',
                        
                        // 模型
                        'model' => common\domain\model\common_user::class,
                        
                        'prefix' => 'q_',
                        'cookie' => 'auth',
                        'session' => 'auth',
                        'field' => 'id,name,nikename,email,mobile' 
                ],
        ] ,

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [ 
                'web' => [ 
                        'driver' => 'session',
                        'provider' => 'users' 
                ],
                
                'api' => [ 
                        'driver' => 'token',
                        'provider' => 'users' 
                ] 
        ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [ 
                'users' => [ 
                        'driver' => 'eloquent',
                        'model' => App\User::class 
                ] 
        ],
        
        // 'users' => [
        // 'driver' => 'database',
        // 'table' => 'users',
        // ],
        
        /*
         * |--------------------------------------------------------------------------
         * | Resetting Passwords
         * |--------------------------------------------------------------------------
         * |
         * | Here you may set the options for resetting passwords including the view
         * | that is your password reset e-mail. You may also set the name of the
         * | table that maintains all of the reset tokens for your application.
         * |
         * | You may specify multiple password reset configurations if you have more
         * | than one user table or model in the application and you want to have
         * | separate password reset settings based on the specific user types.
         * |
         * | The expire time is the number of minutes that the reset token should be
         * | considered valid. This security feature keeps tokens short-lived so
         * | they have less time to be guessed. You may change this as needed.
         * |
         */
        
        'passwords' => [ 
                'users' => [ 
                        'provider' => 'users',
                        'email' => 'auth.emails.password',
                        'table' => 'password_resets',
                        'expire' => 60 
                ] 
        ] 
];

