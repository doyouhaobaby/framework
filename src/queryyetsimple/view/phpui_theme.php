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
namespace queryyetsimple\view;

/**
 * phpui 模板处理类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.21
 * @version 1.0
 */
class phpui_theme extends theme
{

    /**
     * 加载视图文件
     *
     * @param string $sFile 视图文件地址
     * @param boolean $bDisplay 是否显示
     * @param string $strExt 后缀
     * @param string $sTargetCache 主模板缓存路径
     * @param string $sMd5 源文件地址 md5 标记
     * @return string
     */
    public function display($sFile, $bDisplay = true, $strExt = '', $sTargetCache = '', $sMd5 = '')
    {
        // 加载视图文件
        $sFile = $this->parseDisplayFile($sFile, $strExt);

        // 变量赋值
        if (is_array($this->arrVar) && ! empty($this->arrVar)) {
            extract($this->arrVar, EXTR_PREFIX_SAME, 'q_');
        }

        // 返回类型
        if ($bDisplay === false) {
            return include $sFile;
        } else {
            include $sFile;
        }
    }
}