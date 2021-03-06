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
 * 树数据处理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.11
 * @version 1.0
 */
class tree implements itree, ijson, iarray
{

    /**
     * 子父关系映射
     *
     * @var array
     */
    protected $arrMap = [];

    /**
     * 节点数据
     *
     * @var array
     */
    protected $arrData = [];

    /**
     * 构造函数
     *
     * @param array $arrNodes
     * @return void
     */
    public function __construct($arrNodes = [])
    {
        foreach ($arrNodes as $arrNode) {
            $this->setNode($arrNode[0], $arrNode[1], $arrNode[2]);
        }
    }

    /**
     * 设置节点数据
     *
     * @param int $nId
     * @param int $nParent
     * @param mixed $mixValue
     * @param boolean $booPriority
     * @return void
     */
    public function setNode($nId, $nParent, $mixValue, $booPriority = false)
    {
        $nParent = $nParent ? $nParent : 0;
        $this->arrData[$nId] = $mixValue;

        if ($booPriority) {
            $arr = [
                $nId => $nParent
            ];

            foreach ($this->arrMap as $intK => $intV) {
                $arr[$intK] = $intV;
            }
            $this->arrMap = $arr;
            unset($arr);
        } else {
            $this->arrMap[$nId] = $nParent;
        }
    }

    /**
     * 取得给定 ID 子树
     *
     * @param int $nId
     * @return array
     */
    public function getChildrenTree($nId = 0)
    {
        $arrChildren = [];
        foreach ($this->arrMap as $nChild => $nParent) {
            if ($nParent == $nId) {
                $arrChildren[$nChild] = $this->getChildrenTree($nChild);
            }
        }
        return $arrChildren;
    }

    /**
     * 取得给定 ID 一级子树 ID
     *
     * @param int $nId
     * @return array
     */
    public function getChild($nId)
    {
        $arrChild = [];
        foreach ($this->arrMap as $nChild => $nParent) {
            if ($nParent == $nId) {
                $arrChild[$nChild] = $nChild;
            }
        }
        return $arrChild;
    }

    /**
     * 取得给定 ID 所有子树 ID
     *
     * @param int $nId
     * @return array
     */
    public function getChildren($nId = 0)
    {
        $arrChild = [];
        foreach ($this->getChild($nId) as $nChild) {
            $arrChild[] = $nChild;
            $arrChild = array_merge($arrChild, $this->getChildren($nChild));
        }
        return $arrChild;
    }

    /**
     * 取得给定 ID 是否包含子树
     *
     * @param int $nId
     * @return boolean
     */
    public function hasChild($nId)
    {
        return count($this->getChild($nId)) > 0;
    }

    /**
     * 验证是否存在子菜单
     *
     * @param int $intId
     * @param array $arrCheckChildren
     * @param boolean $booStrict
     * @return boolean
     */
    public function hasChildren($intId, array $arrCheckChildren = [], $booStrict = true)
    {
        if (empty($arrCheckChildren)) {
            return false;
        }

        $arrChildren = $this->getChildren($intId);

        if ($booStrict === true && array_diff($arrCheckChildren, $arrChildren)) {
            return false;
        }

        if ($booStrict === false && array_intersect($arrCheckChildren, $arrChildren)) {
            return true;
        }

        return true;
    }

    /**
     * 取得给定 ID 上级父级 ID
     *
     * @param int $nId
     * @param boolean $booWithItSelf
     * @return array
     */
    public function getParent($nId, $booWithItSelf = false)
    {
        $arrParent = [];
        if (array_key_exists($this->arrMap[$nId], $this->arrMap)) {
            $arrParent[] = $this->arrMap[$nId];
        }

        if ($booWithItSelf === true) {
            $arrParent[] = intval($nId);
        }

        return $arrParent;
    }

    /**
     * 取得给定 ID 所有父级 ID
     *
     * @param int $nId
     * @param boolean $booWithItSelf
     * @return array
     */
    public function getParents($nId, $booWithItSelf = true)
    {
        $arrParent = $this->getParentsReal($nId);
        sort($arrParent);

        if ($booWithItSelf === true) {
            $arrParent[] = intval($nId);
        }

        return $arrParent;
    }

    /**
     * 判断级别
     *
     * @param int $nId
     * @return string
     */
    public function getLevel($nId)
    {
        return count($this->getParentsReal($nId));
    }

    /**
     * 取得节点的值
     *
     * @param int $nId
     * @return mixed
     */
    public function getData($nId, $mixDefault = null)
    {
        return $this->arrData[$nId] ?? $mixDefault;
    }

    /**
     * 设置节点的值
     *
     * @param int $nId
     * @param mixed $mixValue
     * @return void
     */
    public function setData($nId, $mixValue)
    {
        if (isset($this->arrData[$nId])) {
            $this->arrData[$nId] = $mixValue;
        }
    }

    /**
     * 树转化为数组
     *
     * @param mixed $mixCallable
     * @param array $arrKey
     * @param int $nId
     * @return array
     */
    public function treeToArray($mixCallable = null, $arrKey = [], $nId = 0)
    {
        $arrData = [];
        foreach ($this->getChild($nId) as $nValue) {
            $arrItem = [
                $arrKey['value'] ?? 'value' => $nValue,
                $arrKey['data'] ?? 'data' => $this->arrData[$nValue]
            ];

            if (is_callable($mixCallable)) {
                $mixReturn = call_user_func_array($mixCallable, [
                    $arrItem,
                    $this
                ]);

                if (! is_null($mixReturn)) {
                    $arrItem = $mixReturn;
                }
            }

            if ($arrChildren = $this->treeToArray($mixCallable, $arrKey, $nValue)) {
                $arrItem[$arrKey['children'] ?? 'children'] = $arrChildren;
            }

            $arrData[] = $arrItem;
        }
        return $arrData;
    }

    /**
     * 对象转 JSON
     *
     * @param integer $intOption
     * @return string
     */
    public function toJson($intOption = JSON_UNESCAPED_UNICODE)
    {
        $arrArgs = func_get_args();
        array_shift($arrArgs);
        return json_encode(call_user_func_array([
            $this,
            'toArray'
        ], $arrArgs), $intOption);
    }

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray()
    {
        return call_user_func_array([
            $this,
            'treeToArray'
        ], func_get_args());
    }

    /**
     * 取得给定 ID 所有父级 ID
     *
     * @param int $nId
     * @return array
     */
    protected function getParentsReal($nId)
    {
        $arrParent = [];
        if (array_key_exists($this->arrMap[$nId], $this->arrMap)) {
            $arrParent[] = $this->arrMap[$nId];
            $arrParent = array_merge($arrParent, $this->getParentsReal($this->arrMap[$nId]));
        }
        return $arrParent;
    }
}
