<?php

namespace \ACNS\Parser;

class Parser
{
	public $notice = null;

	const PARSER_ENGINES = [
		'\ACNS\Parser\Engine\XML',
		'\ACNS\Parser\Engine\Echelon'
	];

	public function __construct(string $notice)
	{
		$this->notice = $notice;
	}

	public function parse(): \ACNS\Parser\Result
	{
		foreach (PARSER_ENGINES as $engine) {

			$acns = new $engine($this->notice);
			$result = $acns->parse();

			if ($result->processed) {
				return $result;
			}
		}

		return \ACNS\Parser\Result::failure();
	}
}

