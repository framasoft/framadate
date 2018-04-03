<?php
namespace Framadate\Services;

use Framadate\FramaTestCase;

class InputServiceUnitTest extends FramaTestCase
{
	/**
	 * @test
	 * @dataProvider liste_emails
	 */
	function test_filterMail($email, $expected) {
		$inputService = new InputService();
		$filtered = $inputService->filterMail($email);
		
		$this->assertSame($expected, $filtered);
	}
	
	function liste_emails() {
		return [
			// valids addresses
			"valid address" 		=> ["example@example.com", "example@example.com"],
			"local address"			=> ["test@localhost", "test@localhost"],
			"IP address"			=> ["ip.email@127.0.0.1", "ip.email@127.0.0.1"],
			"with spaces arround" 	=> ["  with@spaces  ", "with@spaces"],
			"unicode caracters" 	=> ["unicode.éà@idn-œ.com", "unicode.éà@idn-œ.com"],
			// invalids addresses
			"without domain" 		=> ["without-domain", FALSE],
			"space inside" 			=> ["example example@example.com", FALSE],
			"forbidden chars" 		=> ["special_chars.@example.com", FALSE],
		];
	}
}
