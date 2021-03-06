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
namespace tests\filesystem;

use tests\testcase;
use queryyetsimple\filesystem\fso;

/**
 * filesystem 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.01
 * @version 1.0
 */
class Filesystem_test extends testcase
{

    /**
     * 目录
     *
     * @var string
     */
    private $strDir;

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
        $this->strDir = project()->pathRuntime() . '/test_create_dir';
    }

    /**
     * 测试打散目录
     *
     * @return void
     */
    public function testDistributed()
    {
        $this->assertTrue(fso::distributed(1) === [
            '000/00/00/',
            '01'
        ]);
        $this->assertTrue(fso::distributed(1000) === [
            '000/00/10/',
            '00'
        ]);
    }

    /**
     * 测试创建目录和文件
     *
     * @return void
     */
    public function testCreate()
    {
        fso::createDirectory($this->strDir);
        fso::createDirectory($this->strDir . '/test');

        fso::createFile($this->strDir . '/test.txt');
        fso::createFile($this->strDir . '/test/test.txt');

        $this->assertEquals(true, is_dir($this->strDir));
        $this->assertEquals(true, is_dir($this->strDir . '/test'));

        $this->assertEquals(true, is_file($this->strDir . '/test.txt'));
        $this->assertEquals(true, is_file($this->strDir . '/test/test.txt'));
    }

    /**
     * 测试复制目录
     *
     * @return void
     */
    public function testCopy()
    {
        fso::copyDirectory($this->strDir, dirname($this->strDir) . '/test_copy2');
        $this->assertEquals(true, is_file(dirname($this->strDir) . '/test_copy2/test/test.txt'));
    }

    /**
     * 测试读取目录
     *
     * @return void
     */
    public function testList()
    {
        $this->assertEquals([
            'file' => [
                'NewFile.html'
            ],
            'dir' => [
                'test'
            ]
        ], fso::lists(dirname($this->strDir) . '/test_copy2', 'both'));
    }

    /**
     * 测试删除目录
     *
     * @return void
     */
    public function testDelete()
    {
        $this->assertEquals(true, is_dir(dirname($this->strDir) . '/test_copy2'));
        fso::deleteDirectory(dirname($this->strDir) . '/test_copy2', true);
        $this->assertEquals(true, ! is_dir(dirname($this->strDir) . '/test_copy2'));
    }
}
