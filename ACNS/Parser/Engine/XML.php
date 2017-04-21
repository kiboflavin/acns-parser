<?php

namespace \ACNS\Parser\Engine;

class XML extends AbstractEngine
{
	public function parse(): \ACNS\Notice\Result
	{
		if (! preg_match('/(\<\?xml.*<Infringement.*\/Infringement\>)/s', $this->notice_text, $match)) {
			# if we don't find the ACNS XML notice somewhere in the text, return failure
			return \ACNS\Parser\Result::failure();
		}

		$acns_xml = $match[1];

		$sanitized_xml = self::sanitize_text($this->acns_xml);

		if (! $xml = simplexml_load_string($sanitized_xml)) {
			throw new \UnexpectedValueException('Unparsable ACNS XML');
		}

		if (! $xml->Source->IP_Address) {
			throw new \UnexpectedValueException('Unspecified Source IP Address');
		}

		if (! $xml->Source->TimeStamp) {
			throw new \UnexpectedValueException('Unspecified Source Timestamp');
		}

		if (! $xml->Source->Port) {
			throw new \UnexpectedValueException('Unspecified Source Port');
		}

		# FIXME: regex check ip/timestamp

		$result = \ACNS\Parser\Result::success();
		$result->ip_address = (string)$xml->Source->IP_Address;
		$result->port = (string)$xml->Source->Port;
		$result->timestamp = (string)$xml->Source->TimeStamp;
		$result->filename = (string)$xml->Content->Item->FileName;
		$result->type = (string)$xml->Source->Type;
		return $result;
	}

	private function sanitize_xml($xml)
	{
		# viacom notices sometimes don't escape their ampersands
		# convert "&" to "&amp;" but not "&crap;" to "&amp;crap;"
		$sanitized_xml = preg_replace('/&(?!\w+;)/', '&amp;', $xml);

		return $sanitized_xml;
	}
}

# sample notice:
# <?xml version="1.0" encoding="UTF-8">
# <Infringement xmlns="http://www.acns.net/ACNS" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.acns.net/v1.2/ACNS2v1_2.xsd">
#   <Case>
#     <ID>123a6547aa372abcdef0</ID>
#     <Status>Open</Status>
#     <Severity>Normal</Severity>
#   </Case>
#   <Complainant>
#     <Entity>Home Box Office, Inc.</Entity>
#     <Contact>IP-Echelon - Compliance</Contact>
#     <Address>6715 Hollywood Blvd
# Los Angeles CA 90028
# United States of America</Address>
#     <Phone>+1 (310) 606 2747</Phone>
#     <Email>p2p@copyright.ip-echelon.com</Email>
#   </Complainant>
#   <Service_Provider>
#     <Entity>Superduper ISP</Entity>
#     <Email>abuse@superduper.net</Email>
#   </Service_Provider>
#   <Source>
#     <TimeStamp>2016-10-16T17:27:46Z</TimeStamp>
#     <IP_Address>10.239.169.13</IP_Address>
#     <Port>33273</Port>
#     <Type>BitTorrent</Type>
#     <SubType BaseType="P2P" Protocol="BITTORRENT"/>
#     <Number_Files>1</Number_Files>
#   </Source>
#   <Content>
#     <Item>
#       <TimeStamp>2016-10-16T17:27:46Z</TimeStamp>
#       <Title>Banshee</Title>
#       <FileName>Banshee.S01E06.HDTV.x264-2HD.mp4</FileName>
#       <FileSize>387902326</FileSize>
#       <Hash Type="SHA1">c670e4e61eb52ef38ac219d7a82bd3da7946341a</Hash>
#     </Item>
#   </Content>
# </Infringement>

