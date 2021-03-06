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
namespace queryyetsimple\database;

use PDO;
use Throwable;
use Exception;
use PDOException;
use queryyetsimple\{
    log\ilog,
    cache\icache,
    database\select,
    support\debug\dump
};

/**
 * 数据库连接抽象层
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.03.09
 * @version 1.0
 */
abstract class aconnect
{

    /**
     * 所有数据库连接
     *
     * @var array
     */
    protected $arrConnect = [];

    /**
     * 当前数据库连接
     *
     * @var array
     */
    protected $objConnect;

    /**
     * PDO 预处理语句对象
     *
     * @var PDOStatement
     */
    protected $objPDOStatement;

    /**
     * 数据查询组件
     *
     * @var \queryyetsimple\database\select
     */
    protected $objSelect;

    /**
     * 日志仓储
     *
     * @var \queryyetsimple\log\ilog
     */
    protected $objLog;

    /**
     * 缓存仓储
     *
     * @var \queryyetsimple\cache\icache
     */
    protected $objCache;

    /**
     * 开发模式
     *
     * @var boolean
     */
    protected $booDevelopment = false;

    /**
     * 字段缓存
     *
     * @var array
     */
    protected static $arrTableColumnsCache = [];

    /**
     * 数据库连接参数
     *
     * @var array
     */
    protected $arrOption = [];

    /**
     * 当前数据库连接参数
     *
     * @var array
     */
    protected $arrCurrentOption = [];

    /*
     * sql 最后查询语句
     *
     * @var string
     */
    protected $strSql;

    /*
     * sql 绑定参数
     *
     * @var array
     */
    protected $arrBindParams = [];

    /*
     * sql 影响记录数量
     *
     * @var int
     */
    protected $intNumRows = 0;

    /**
     * SQL 监听器
     *
     * @var callable
     */
    protected static $calSqlListen;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\log\ilog $objLog
     * @param \queryyetsimple\cache\icache $objCache
     * @param array $arrOption
     * @param boolean $booDevelopment
     * @return void
     */
    public function __construct(ilog $objLog, icache $objCache, $arrOption, $booDevelopment = false)
    {
        // 日志
        $this->objLog = $objLog;

        // 缓存
        $this->objCache = $objCache;

        // 开发模式
        $this->booDevelopment = $booDevelopment;

        // 记录连接参数
        $this->arrOption = $arrOption;

        // 尝试连接主服务器
        $this->writeConnect();

        // 连接分布式服务器
        if ($arrOption['distributed'] === true) {
            if (! $this->readConnect()) {
                $this->throwException();
            }
        }
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        // 查询组件
        $this->initSelect();

        // 调用事件
        return $this->objSelect->$sMethod(...$arrArgs);
    }

    /**
     * 返回 Pdo 查询连接
     *
     * @param mixed $mixMaster
     * @paramnote boolean false (读服务器) true (写服务器)
     * @paramnote 其它 去对应服务器连接ID 0 表示主服务器
     * @return mixed
     */
    public function getPdo($mixMaster = false)
    {
        if (is_bool($mixMaster)) {
            if ($mixMaster === false) {
                return $this->readConnect();
            } else {
                return $this->writeConnect();
            }
        } else {
            return $this->arrConnect[$mixMaster] ?? null;
        }
    }

    /**
     * 查询数据记录
     *
     * @param string $strSql sql 语句
     * @param array $arrBindParams sql 参数绑定
     * @param mixed $mixMaster
     * @param int $intFetchType
     * @param mixed $mixFetchArgument
     * @param array $arrCtorArgs
     * @return mixed
     */
    public function query($strSql, $arrBindParams = [], $mixMaster = false, $intFetchType = null, $mixFetchArgument = null, $arrCtorArgs = [])
    {
        // 查询组件
        $this->initSelect();

        // 记录 sql 参数
        $this->setSqlBindParams($strSql, $arrBindParams);

        // 验证 sql 类型PROCEDURE
        if (! in_array(($strSqlType = $this->getSqlType($strSql)), [
            'select',
            'procedure'
        ])) {
            $this->throwException('The query method only allows select SQL statements.');
        }

        // 预处理
        $this->objPDOStatement = $this->getPdo($mixMaster)->prepare($strSql);

        // 参数绑定
        $this->bindParams($arrBindParams);

        // 执行 sql
        if ($this->objPDOStatement->execute() === false) {
            $this->throwException();
        }

        // 记录 SQL 日志
        $this->recordSqlLog();

        // 返回影响函数
        $this->intNumRows = $this->objPDOStatement->rowCount();

        // 返回结果
        return $this->fetchResult($intFetchType, $mixFetchArgument, $arrCtorArgs, $strSqlType == 'procedure');
    }

    /**
     * 执行 sql 语句
     *
     * @param string $strSql sql 语句
     * @param array $arrBindParams sql 参数绑定
     * @return int
     */
    public function execute($strSql, $arrBindParams = [])
    {
        // 查询组件
        $this->initSelect();

        // 记录 sql 参数
        $this->setSqlBindParams($strSql, $arrBindParams);

        // 验证 sql 类型
        if (($strSqlType = $this->getSqlType($strSql)) == 'select') {
            $this->throwException('The execute method does not allow select SQL statements.');
        }

        // 预处理
        $this->objPDOStatement = $this->getPdo(true)->prepare($strSql);

        // 参数绑定
        $this->bindParams($arrBindParams);

        // 执行 sql
        if ($this->objPDOStatement->execute() === false) {
            $this->throwException();
        }

        // 记录 SQL 日志
        $this->recordSqlLog();

        // 返回影响函数
        $this->intNumRows = $this->objPDOStatement->rowCount();

        if (in_array($strSqlType, [
            'insert',
            'replace'
        ])) {
            return $this->lastInsertId();
        } else {
            return $this->intNumRows;
        }
    }

    /**
     * 执行数据库事务
     *
     * @param callable $calAction 事务回调
     * @return mixed
     */
    public function transaction(callable $calAction)
    {
        // 事务过程
        $this->beginTransaction();
        try {
            $mixResult = call_user_func_array($calAction, [
                $this
            ]);
            $this->commit();
            return $mixResult;
        } catch (Throwable $oE) {
            $this->rollBack();
            throw $oE;
        }
    }

    /**
     * 启动事务
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->getPdo(true)->beginTransaction();
    }

    /**
     * 检查是否处于事务中
     *
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->getPdo(true)->inTransaction();
    }

    /**
     * 用于非自动提交状态下面的查询提交
     *
     * @return void
     */
    public function commit()
    {
        $this->getPdo(true)->commit();
    }

    /**
     * 事务回滚
     *
     * @return void
     */
    public function rollBack()
    {
        $this->getPdo(true)->rollBack();
    }

    /**
     * 获取最后插入 ID 或者列
     *
     * @param string $strName 自增序列名
     * @return string
     */
    public function lastInsertId($strName = null)
    {
        return $this->objConnect->lastInsertId($strName);
    }

    /**
     * 获取最近一次查询的 sql 语句
     *
     * @param bool $booWithBindParams 是否和绑定参数一起返回
     * @return string
     */
    public function getLastSql($booWithBindParams = false)
    {
        if ($booWithBindParams === true) {
            return [
                $this->strSql,
                $this->arrBindParams
            ];
        } else {
            return $this->strSql;
        }
    }

    /**
     * 获取最近一次绑定参数
     *
     * @return array
     */
    public function getBindParams()
    {
        return $this->arrBindParams;
    }

    /**
     * 返回影响记录
     *
     * @return int
     */
    public function getNumRows()
    {
        return $this->intNumRows;
    }

    /**
     * 注册 SQL 监视器
     *
     * @param callable $calSqlListen
     * @return void
     */
    public function registerListen(callable $calSqlListen)
    {
        static::$calSqlListen = $calSqlListen;
    }

    /**
     * 释放 PDO 预处理查询
     *
     * @return void
     */
    public function freePDOStatement()
    {
        $this->objPDOStatement = null;
    }

    /**
     * 关闭数据库连接
     *
     * @return void
     */
    public function closeDatabase()
    {
        $this->arrConnect = [];
        $this->objConnect = null;
    }

    /**
     * 取得数据库表字段信息缓存
     *
     * @param string $sTableName
     * @param mixed $mixMaster
     * @return array
     */
    public function getTableColumnsCache($sTableName, $mixMaster = false)
    {
        $strCacheKey = sprintf('%s_%s', 'table_columns', $sTableName);
        if (isset(static::$arrTableColumnsCache[$strCacheKey])) {
            return static::$arrTableColumnsCache[$strCacheKey];
        }

        $arrTableColumns = $this->objCache->get($strCacheKey);
        if (! $this->booDevelopment && $arrTableColumns !== false) {
            return static::$arrTableColumnsCache[$strCacheKey] = $arrTableColumns;
        } else {
            $arrTableColumns = $this->getTableColumns($sTableName, $mixMaster);
            $this->objCache->set($strCacheKey, $arrTableColumns);
            return static::$arrTableColumnsCache[$strCacheKey] = $arrTableColumns;
        }
    }

    /**
     * sql 表达式格式化
     *
     * @param string $sSql
     * @param string $sTableName
     * @param array $arrMapping
     * @return string
     */
    public function qualifyExpression($sSql, $sTableName, array $arrMapping = null)
    {
        if (empty($sSql)) {
            return '';
        }

        $arrMatches = null;
        preg_match_all('/\[[a-z][a-z0-9_\.]*\]|\[\*\]/i', $sSql, $arrMatches, PREG_OFFSET_CAPTURE);
        $arrMatches = reset($arrMatches);
        if (! is_array($arrMapping)) {
            $arrMapping = [];
        }

        $sOut = '';
        $nOffset = 0;
        foreach ($arrMatches as $arrM) {
            $nLen = strlen($arrM[0]);
            $sField = substr($arrM[0], 1, $nLen - 2);
            $arrArray = explode('.', $sField);
            switch (count($arrArray)) {
                case 3:
                    $sF = ! empty($arrMapping[$arrArray[2]]) ? $arrMapping[$arrArray[2]] : $arrArray[2];
                    $sTable = "{$arrArray[0]}.{$arrArray[1]}";
                    break;
                case 2:
                    $sF = ! empty($arrMapping[$arrArray[1]]) ? $arrMapping[$arrArray[1]] : $arrArray[1];
                    $sTable = $arrArray[0];
                    break;
                default:
                    $sF = ! empty($arrMapping[$arrArray[0]]) ? $arrMapping[$arrArray[0]] : $arrArray[0];
                    $sTable = $sTableName;
            }
            $sField = $this->qualifyTableOrColumn("{$sTable}.{$sF}");
            $sOut .= substr($sSql, $nOffset, $arrM[1] - $nOffset) . $sField;
            $nOffset = $arrM[1] + $nLen;
        }
        $sOut .= substr($sSql, $nOffset);

        return $sOut;
    }

    /**
     * 表或者字段格式化（支持别名）
     *
     * @param string $sName
     * @param string $sAlias
     * @param string $sAs
     * @return string
     */
    public function qualifyTableOrColumn($sName, $sAlias = null, $sAs = null)
    {
        $sName = str_replace('`', '', $sName); // 过滤'`'字符
        if (strpos($sName, '.') === false) { // 不包含表名字
            $sName = $this->identifierColumn($sName);
        } else {
            $arrArray = explode('.', $sName);
            foreach ($arrArray as $nOffset => $sName) {
                if (empty($sName)) {
                    unset($arrArray[$nOffset]);
                } else {
                    $arrArray[$nOffset] = $this->identifierColumn($sName);
                }
            }
            $sName = implode('.', $arrArray);
        }
        if ($sAlias) {
            return "{$sName} {$sAs} " . $this->identifierColumn($sAlias);
        } else {
            return $sName;
        }
    }

    /**
     * 字段格式化
     *
     * @param string $sKey
     * @param string $sTableName
     * @return string
     */
    public function qualifyColumn($sKey, $sTableName)
    {
        if (strpos($sKey, '.')) {
            // 如果字段名带有 .，则需要分离出数据表名称和 schema
            $arrKey = explode('.', $sKey);
            switch (count($arrKey)) {
                case 3:
                    $sField = $this->qualifyTableOrColumn("{$arrKey[0]}.{$arrKey[1]}.{$arrKey[2]}");
                    break;
                case 2:
                    $sField = $this->qualifyTableOrColumn("{$arrKey[0]}.{$arrKey[1]}");
                    break;
            }
        } else {
            $sField = $this->qualifyTableOrColumn("{$sTableName}.{$sKey}");
        }
        return $sField;
    }

    /**
     * 字段值格式化
     *
     * @param boolean $booQuotationMark
     * @param mixed $mixValue
     * @return mixed
     */
    public function qualifyColumnValue($mixValue, $booQuotationMark = true)
    {
        if (is_array($mixValue)) { // 数组，递归
            foreach ($mixValue as $nOffset => $sV) {
                $mixValue[$nOffset] = $this->qualifyColumnValue($sV);
            }
            return $mixValue;
        }

        if (is_int($mixValue)) {
            return $mixValue;
        }
        if (is_bool($mixValue)) {
            return $mixValue ? true : false;
        }
        if (is_null($mixValue)) {
            return null;
        }

        $mixValue = trim($mixValue);

        // 问号占位符
        if ($mixValue == '[?]') {
            return '?';
        }

        // [:id] 占位符
        if (preg_match('/^\[:[a-z][a-z0-9_\-\.]*\]$/i', $mixValue, $arrMatche)) {
            return trim($arrMatche[0], '[]');
        }

        if ($booQuotationMark === true) {
            return "'" . addslashes($mixValue) . "'";
        } else {
            return $mixValue;
        }
    }

    /**
     * 返回当前配置连接信息（方便其他组件调用设置为 public）
     *
     * @param string $strOptionName
     * @return array
     */
    public function getCurrentOption($strOptionName = null)
    {
        if (is_null($strOptionName)) {
            return $this->arrCurrentOption;
        } else {
            return $this->arrCurrentOption[$strOptionName] ?? null;
        }
    }

    /**
     * 分析 sql 类型数据
     *
     * @param string $strSql
     * @return string
     */
    public function getSqlType($strSql)
    {
        $strSql = trim($strSql);
        foreach ([
            'select',
            'show',
            'call',
            'exec',
            'delete',
            'insert',
            'replace',
            'update'
        ] as $strType) {
            if (stripos($strSql, $strType) === 0) {
                if ($strType == 'show') {
                    $strType = 'select';
                } elseif (in_array($strType, [
                    'call',
                    'exec'
                ])) {
                    $strType = 'procedure';
                }
                return $strType;
            }
        }
        return 'statement';
    }

    /**
     * 分析绑定参数类型数据
     *
     * @see http://php.net/manual/en/pdo.constants.php
     * @param mixed $mixValue
     * @return string
     */
    public function getBindParamType($mixValue)
    {
        // 参数
        switch (true) {
            case is_int($mixValue):
                return PDO::PARAM_INT;
                break;
            case is_bool($mixValue):
                return PDO::PARAM_BOOL;
                break;
            case is_null($mixValue):
                return PDO::PARAM_NULL;
                break;
            case is_string($mixValue):
                return PDO::PARAM_STR;
                break;
            default:
                return PDO::PARAM_STMT;
                break;
        }
    }

    /**
     * 连接主服务器
     *
     * @return Pdo
     */
    protected function writeConnect()
    {
        // 判断是否已经连接
        if (! empty($this->arrConnect[0])) {
            return $this->objConnect = $this->arrConnect[0];
        }

        // 没有连接开始请求连接
        $objPdo = $this->commonConnect($this->arrOption['master'], 0, true);

        // 当前连接
        return $this->objConnect = $objPdo;
    }

    /**
     * 连接读服务器
     *
     * @return Pdo
     */
    protected function readConnect()
    {
        // 未开启分布式服务器连接或则没有读服务器，直接连接写服务器
        if ($this->arrOption['distributed'] === false || empty($this->arrOption['slave'])) {
            return $this->writeConnect();
        }

        // 只有主服务器,主服务器必须先连接,未连接过附属服务器
        if (count($this->arrConnect) == 1) {
            foreach ($this->arrOption['slave'] as $arrRead) {
                $this->commonConnect($arrRead, null);
            }

            // 没有连接成功的读服务器则还是连接写服务器
            if (count($this->arrConnect) < 2) {
                return $this->writeConnect();
            }
        }

        // 如果为读写分离,去掉主服务器
        $arrConnect = $this->arrConnect;
        if ($this->arrOption['readwrite_separate'] === true) {
            unset($arrConnect[0]);
        }

        // 随机在已连接的 slave 服务器中选择一台
        return $this->objConnect = $arrConnect[floor(mt_rand(0, count($arrConnect) - 1))];
    }

    /**
     * 连接数据库
     *
     * @param array $arrOption
     * @param string $nLinkid
     * @param boolean $booThrowException
     * @return mixed
     */
    protected function commonConnect($arrOption = '', $nLinkid = null, $booThrowException = false)
    {
        // 数据库连接 ID
        if ($nLinkid === null) {
            $nLinkid = count($this->arrConnect);
        }

        // 已经存在连接
        if (! empty($this->arrConnect[$nLinkid])) {
            return $this->arrConnect[$nLinkid];
        }

        try {
            $this->setCurrentOption($arrOption);
            return $this->arrConnect[$nLinkid] = new PDO($this->parseDsn($arrOption), $arrOption['user'], $arrOption['password'], $arrOption['options']);
        } catch (PDOException $oE) {
            if ($booThrowException === false) {
                return false;
            } else {
                throw $oE;
            }
        }
    }

    /**
     * pdo　参数绑定
     *
     * @param array $arrBindParams 绑定参数
     * @return void
     */
    protected function bindParams(array $arrBindParams = [])
    {
        foreach ($arrBindParams as $mixKey => $mixVal) {
            $mixKey = is_numeric($mixKey) ? $mixKey + 1 : ':' . $mixKey;

            if (is_array($mixVal)) {
                $strParam = $mixVal[1];
                $mixVal = $mixVal[0];
            } else {
                $strParam = PDO::PARAM_STR;
            }

            if ($this->objPDOStatement->bindValue($mixKey, $mixVal, $strParam) === false) {
                $this->throwException(sprintf('Parameter of sql %s binding failed: %s.', $this->strSql, dump::dump($arrBindParams, true)));
            }
        }
    }

    /**
     * 获得数据集
     *
     * @param int $intFetchType
     * @param mixed $mixFetchArgument
     * @param array $arrCtorArgs
     * @param boolean $booProcedure
     * @return array
     */
    protected function fetchResult($intFetchType = null, $mixFetchArgument = null, $arrCtorArgs = [], $booProcedure = false)
    {
        // 存储过程支持多个结果
        if ($booProcedure) {
            return $this->fetchProcedureResult($intFetchType, $mixFetchArgument, $arrCtorArgs);
        }

        $arrArgs = [
            $intFetchType !== null ? $intFetchType : $this->arrOption['fetch']
        ];
        if ($mixFetchArgument) {
            $arrArgs[] = $mixFetchArgument;
            if ($arrCtorArgs) {
                $arrArgs[] = $arrCtorArgs;
            }
        }

        return $this->objPDOStatement->{'fetchAll'}(...$arrArgs);
    }

    /**
     * 获得数据集
     *
     * @param int $intFetchType
     * @param mixed $mixFetchArgument
     * @param array $arrCtorArgs
     * @return array
     */
    protected function fetchProcedureResult($intFetchType = null, $mixFetchArgument = null, $arrCtorArgs = [])
    {
        $arrResult = [];
        do {
            if (($mixResult = $this->fetchResult($intFetchType, $mixFetchArgument, $arrCtorArgs))) {
                $arrResult[] = $mixResult;
            }
        } while ($this->objPDOStatement->nextRowset());
        return $arrResult;
    }

    /**
     * 设置 sql 绑定参数
     *
     * @return void
     */
    protected function setSqlBindParams($strSql, $arrBindParams = [])
    {
        $this->strSql = $strSql;
        $this->arrBindParams = $arrBindParams;
    }

    /**
     * 设置当前数据库连接信息
     *
     * @param array $arrOption
     * @return void
     */
    protected function setCurrentOption($arrOption)
    {
        $this->arrCurrentOption = $arrOption;
    }

    /**
     * 记录 SQL 日志
     *
     * @return void
     */
    protected function recordSqlLog()
    {
        // SQL 监视器
        if (static::$calSqlListen !== null) {
            call_user_func_array(static::$calSqlListen, [
                $this
            ]);
        }

        // 记录 SQL 日志
        $arrLastSql = $this->getLastSql(true);
        if ($this->arrOption['log']) {
            $this->objLog->log(ilog::SQL, $arrLastSql[0], $arrLastSql[1] ?  : []);
        }
    }

    /**
     * 数据查询异常，抛出错误
     *
     * @param string $strError 错误信息
     * @return void
     */
    protected function throwException($strError = '')
    {
        if ($this->objPDOStatement) {
            $arrTemp = $this->objPDOStatement->errorInfo();
            $strError = '(' . $arrTemp[1] . ')' . $arrTemp[2] . "\r\n" . $strError;
            throw new PDOException($strError);
        } else {
            throw new Exception($strError);
        }
    }

    /**
     * 初始化查询组件
     *
     * @return void
     */
    protected function initSelect()
    {
        $this->objSelect = new select($this);
    }
    
    /**
     * 析构方法
     *
     * @return void
     */
    public function __destruct()
    {
        // 释放 PDO 预处理查询
        $this->freePDOStatement();

        // 关闭数据库连接
        $this->closeDatabase();
    }
}
