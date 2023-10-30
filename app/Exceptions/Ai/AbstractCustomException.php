<?php

namespace App\Exceptions\Ai;

use Exception;
use Illuminate\Support\Facades\Log;

abstract class AbstractCustomException extends Exception
{
    protected $is_report_called;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->is_report_called = false;
        parent::__construct($message, $code, $previous);
        $this->callReport();
    }

    protected function reportCalledOnce()
    {
        $this->is_report_called = true;
    }

    abstract protected function report();

    protected function callReport()
    {
        if ($this->is_report_called) {
            return;
        }
        $this->report();
        $this->reportCalledOnce();
    }
}
