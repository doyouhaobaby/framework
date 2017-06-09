<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue\backend;

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

use PHPQueue\Backend\Predis;

/**
 * redis 存储
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.08
 * @version 1.0
 */
class redis extends Predis {
    
    /**
     * (non-PHPdoc)
     *
     * @see \PHPQueue\Backend\Predis::release()
     */
    public function release($jobId = null) {
        $this->beforeRelease ( $jobId );
        if (! $this->hasQueue ()) {
            throw new BackendException ( "No queue specified." );
        }
        $strJobData = $this->open_items [$jobId];
        
        // 加入执行次数
        $strJobData = json_decode ( $strJobData, true );
        if ($strJobData) {
            if (empty ( $strJobData ['data'] ['attempts'] ))
                $strJobData ['data'] ['attempts'] = 1;
            else
                $strJobData ['data'] ['attempts'] ++;
            $strJobData = json_encode ( $strJobData );
        }
        
        $booStatus = $this->getConnection ()->rpush ( $this->queue_name, $strJobData );
        if (! $booStatus) {
            throw new BackendException ( "Unable to save data." );
        }
        $this->last_job_id = $jobId;
        $this->afterClearRelease ();
    }
}