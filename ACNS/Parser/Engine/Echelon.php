<?php

namespace \ACNS\Parser\Engine;

class Echelon extends AbstractEngine
{
	public function parse(): \ACNS\Notice\Result
	{
		if (! preg_match('/-+ Infringement Details -+<br>(.*?)-+<br>/s', $this->notice_text, $match)) {
            # if we don't find Echelon's version of the notice somewhere in the text, return failure
    		return \ACNS\Parser\Result::failure();
		}

		$acns = $match[1];

		if (! preg_match('/^IP Address: (.*)<br>$/m', $acns, $match)) {
			throw new UnexpectedValueException('No ip_address property found');
		}

		# TODO: regex check that IP address is valid

		$result = \ACNS\Parser\Result::success();
		$result->ip_address = $match[1];

		if (! preg_match('/^Port: (.*)<br>$/m', $acns, $match)) {
			throw new UnexpectedValueException('No port property found');
		}

		$result->port = $match[1];

		if (! preg_match('/^Timestamp: ([\d\-]+) ([\d\:]+)<br>$/m', $acns, $match)) {
			throw new UnexpectedValueException('No timestamp property found');
		}

		$result->timestamp = $match[1]. 'T'. $match[2]. 'Z';

		# TODO: validate timestamp is actually valid

		if (! preg_match('/^Filename: (.*)<br>$/m', $acns, $match)) {
			throw new UnexpectedValueException('No filename property found');
		}

		$result->filename = $match[1];

		if (! preg_match('/^Type: (.*)<br>$/m', $acns, $match)) {
			throw new UnexpectedValueException('No type property found');
		}

		$result->type = $match[1];

		return $result;
	}
}

# sample notice:
#
# -------------- Infringement Details ----------------------------------
# Title: The Nice Guys
# IP Address: 10.85.139.147
# Port: 60732
# Timestamp: 2016-07-09 02:01:58
# Type: BitTorrent
# BitTorrent Torrent Hash: 069A9B84542CCA0BEEEDBFC737F0DE04D47F3919
# Filename: The Nice Guys 2016 HC HDRip XviD AC3-EVO
# Filesize: 1.42 GB
# ---------------------------------------------------------------------

