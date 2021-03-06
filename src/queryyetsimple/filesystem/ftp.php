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
namespace queryyetsimple\filesystem;

use League\Flysystem\Adapter\Ftp as AdapterFtp;

/**
 * filesystem.ftp
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/ftp/
 * @version 1.0
 */
class ftp extends aconnect implements iconnect
{

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        // 主机
        'host' => 'ftp.example.com',

        // 端口
        'port' => 21,

        // 用户名
        'username' => 'your-username',

        // 密码
        'password' => 'your-password',

        // 根目录
        'root' => '',

        // 被动、主动
        'passive' => true,

        // 加密传输
        'ssl' => false,

        // 超时设置
        'timeout' => 20
    ];

    /**
     * 创建连接
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect()
    {
        return new AdapterFtp($this->getOptions());
    }
}
