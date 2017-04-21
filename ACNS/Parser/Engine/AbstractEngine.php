<?php

namespace \ACNS\Parser\Engine;

abstract AbstractEngine
{
	abstract public function parse(): \ACNS\Notice\Result;
}

