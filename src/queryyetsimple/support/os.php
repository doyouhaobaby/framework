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
namespace queryyetsimple\support;

/**
 * 操作系统类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.05
 * @version 1.0
 */
class os
{

    /**
     * 是否为 window 平台
     *
     * @return boolean
     */
    public static function isWindows()
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * 是否为 Linux 平台
     *
     * @return boolean
     */
    public static function isLinux()
    {
        return PHP_OS === 'Linux';
    }

    /**
     * 是否为 mac 平台
     *
     * @return boolean
     */
    public static function isMac()
    {
        return strstr(PHP_OS, 'Darwin');
    }

    /**
     * 返回操作系统名称
     *
     * @return string
     */
    public static function osName()
    {
        return PHP_OS;
    }

    /**
     * 当前操作系统换行符
     *
     * @return string
     */
    public static function osNewline()
    {
        return PHP_EOL;
    }
}
