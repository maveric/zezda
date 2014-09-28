<?php

namespace Zezda\AlertBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParseControllerTest extends WebTestCase
{
	public function testAlertparse()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/alert/parse/alert');
	}

}
