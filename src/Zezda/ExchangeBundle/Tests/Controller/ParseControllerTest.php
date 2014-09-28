<?php

namespace Zezda\ExchangeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParseControllerTest extends WebTestCase
{
	public function testParse()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/exchange/parse');
	}

}
