<?php

namespace \ACNS\Parser;

class Result
{
	public $processed = false;
	public $ip_address = null;
	public $port = null;
	public $timestamp = null;
	public $filename = null;
	public $type = null;

	public function __construct(bool $processed)
	{
		$this->processed = $processed;
	}

	public static function success()
	{
		return new Result(true);
	}

	public static function failure()
	{
		return new Result(false);
	}
}
