<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/21/2015
 * Time: 10:27 PM
 */

namespace App\Libraries;
use \Swiftlet\Abstracts\Library as LibraryAbstract;

class Performance  extends LibraryAbstract {

    /**
     * @var float
     */
    protected $startExecutionTime;

    /**
     * @var float
     */
    protected $endExecutionTime;

    /**
     * @return mixed
     */
    public function getEndExecutionTime()
    {
        return $this->endExecutionTime;
    }

    /**
     * @return mixed
     */
    public function getStartExecutionTime()
    {
        return $this->startExecutionTime;
    }

    /**
     * @param mixed $startExecutionTime
     */
    public function setStartExecutionTime($startExecutionTime)
    {
        $this->startExecutionTime = $startExecutionTime;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->startExecutionTime = $this->getTime();
    }

    /**
     * Get time
     * @return array|mixed
     */
    public function getTime()
    {
        $time = microtime(true);
        return $time;
    }

    /**
     * Get execution time in seconds
     * @return float
     */
    public function getExecutionTime()
    {
        $total_time = round(($this->getTime() - $this->startExecutionTime), 4);
        return $total_time;
    }

    /**
     * Get memory usage
     * @return string
     */
    public function getMemoryUsage()
    {
        $mem_usage = memory_get_usage(true);

        if ($mem_usage < 1024)
            return $mem_usage." B";
        elseif ($mem_usage < 1048576)
            return round($mem_usage/1024,2)." KB";
        else
            return round($mem_usage/1048576,2)." MB";

    }

}