<?php
/**
 * @TODO: once stocks are all loaded into the system, add calculated candle to the alerts / parsing process
 */

namespace Zezda\AlertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

//Our uses:
use Zezda\AlertBundle\Entity\Alert;
use Zezda\ExchangeBundle\Entity\Stock;
use Zezda\AlertBundle\Entity\AlertContent;
use Zezda\AlertBundle\Entity\Watchlist;

class ParseController extends Controller
{
	private $smsFilePath = "/home/zezda/projects/zezda/sms.xml";
	
	public function getSmsFileRPath()
	{
		return $this->smsFilePath;
	}
	public function setSmsFilePath($path)
	{
		$this->smsFilePath = $path;
	}

	/**
	 * @Route("/alert/parse/sms" )
	 * @Template()
	 *
	 * Read the text message file, and insert the alerts into the databse.
	 *
	 * @todo: (linking to candles and such later)
	 */
	public function SmsAlertParseAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$xml = simplexml_load_file($this->smsFilePath);
		$defaultStock = $em->getRepository('ZezdaExchangeBundle:Stock')
			->findOneByTicker('holder');

		foreach ($xml->sms as $sms)
		{


			//Body now holds the message contents from the text message we're currently working on
			$body = (string)$sms->attributes()->body;

			//Explode around ":" which is the seperator for all alert texts.

			$bodyArray = explode(":", $body, 2);

			//Find the newsletter this text belongs to (if any)
			$newsletter = $em->getRepository('ZezdaAlertBundle:Newsletter')
				->findOneBy(array(
					'smsPrefix' => $bodyArray[0],
				));


			$datetime = new \Datetime();
			$time = substr((string)$sms->attributes()->date, 0, 10);
			$datetime = $datetime->setTimestamp($time);


			/*
			 * If the newsletter gets pulled then this is a valid text alert, lets continue with the processing
			 */
			if (isset($newsletter))
			{


				/*
				 * Stuff that needs to be done to every SMS alert, no matter who its from
				 */

				$alert = $this->parseSmsAlertGeneral($newsletter, $bodyArray[1], $datetime, $defaultStock);

				/*
				 * For this we currently only want to deal with profitly-style alerts - we'll auto-make the set.
				 */
				if ($newsletter->getIsProfitly() == 1)
				{
					/*
					 * If it's a profitly alert, we can pull more data for it. Do so.
					 */
					$alert = $this->parseAlertProfitly($alert);
				} /*
                 * Newsletter is still valid, but we're not dealing with a profitly style alert.
                 * Put the alert in the system, but do not auto create the set
                 */
				elseif ($newsletter->getIsProfitly() == 0)
				{
					//Anything that comes up tha tneeds to be put in a non-profitly specific call
				} /*
				 * Not a valid newsletter. Skip it... or something.
				 */
				else
				{
					// echo "not alert.<br>";

				}


				/*
				 * Make sure that this alert is not a duplicate, then submit it to be put into the database
				 */

				$this->checkDoubleAlert($alert);
			}

		}

		//We've exited the for loop. flush and return to some type of results page.
		echo "this is the end of the road jack <br>";
		$em->flush();
		echo "done flushing<br>";

		//return results


	}

	/**
	 * @todo: find a way to not have password hardcoded. Evntually going to need it's own email address
	 * @todo: Only pull e-mails since last e-mail alert.
	 *
	 * @Route("/alert/parse/email" )
	 *
	 * Connect to the zezda gmail account and parse all e-mails from respective newsletters
	 * Profitly emails are parsed completely and entered into the DB
	 *
	 */

	public function emailAlertParseAction()
	{

		/*
		 * Define variables needed for this function to work
		 */
		//connect to gmail
		$hostname = '{imap.gmail.com:993/imap/ssl}[Gmail]/All Mail';
		$username = $this->container->getParameter('imap.user');
		$password = $this->container->getParameter('imap.pass');
		//Entity manager
		$em = $this->getDoctrine()->getManager();

		//Get all newsletters - needed for later functioins
		$newsletters = $em->getRepository('ZezdaAlertBundle:Newsletter')
			->FindAll();

		//get the default stock
		$defaultStock = $em->getRepository('ZezdaExchangeBundle:Stock')
			->findOneByTicker('holder');

		//Get the most recent email alert
		$lastEmailAlert = $em->getRepository('ZezdaAlertBundle:Alert')
			->findOneBy(array(
				'type'=>2,
				),
				array(
					'datetime' => 'DESC'
				)
			);


		/* try to connect */
		$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

		/* grab emails */
		$emails = imap_search($inbox, 'SINCE "'.$lastEmailAlert->getDatetime()->format('d M Y') .'"');

		/* if emails are returned, cycle through each... */
		if ($emails)
		{
			//rsort($emails);
			$count = 0;
			//We have to go through every e-mail individually
			foreach ($emails as $email)
			{

				/*
				 * Grab the info from this message so we can use it in our alerts
				 */
				$overview = imap_fetch_overview($inbox, $email, 0);
				$message = quoted_printable_decode(imap_fetchbody($inbox, $email, 2));

				/*
				 * alert will be false if it's from a non-alerting source (wont enter if statement)
				 * If it's from an alerting source, alert holds an Alert object we can start to use.
				 */
				//make sure it's "from" someone...
				//if (isset($overview[0]->from) && !empty($overview[0]->from))
				$alert = $this->isEmailAlert($overview[0]->from, $newsletters);
				if (is_object($alert))
				{

					/*
					 * General parsing that is done to every email alert, regardless of who it's from.
					 * Sets newsletter source, datetime, content etc.
					 */

					$parsedAlert = $this->parseEmailGeneral($alert, $message, $overview[0]->date, $defaultStock);
					//echo "newsletter ".$parsedAlert->getNewsletter(). "Date: ".$overview[0]->date ."Way I represent date: ".substr($overview[0]->date, 0, -11)." alert time: ".$alert->getDatetime()->format('Ymd H:i:s')."<br>";
					//If it's a profitly newsletter, we can parse it more completely and fully add it to the databse.
					if ($parsedAlert->getNewsletter()->getIsProfitly())
					{
						//Finish the profitly alert.
						if ($filledAlert = $this->parseEmailAlertProfitly($parsedAlert, $newsletters))
						{
							$this->checkDoubleAlert($filledAlert);
						}
						else
						{
							// if it's not an alert, it's a watchlist. So make one.
							$watchlist = $this->createWatchlist($alert);
							$this->checkDoubleWatchlist($watchlist);
						}


						/*
						 * if it's not a profitly alert, we have extra work to do.
						 */
					}
					else
					{
						//If we need to do anything specific to the non-profitly alerts, we'll do it here.
						//We have $parsedAlert which is full of all of our basic info.
						//echo "from: ".$parsedAlert->getNewsletter()->getName() ."at: ".$parsedAlert->getDatetime()->format('Ymd H:i');
						$this->checkDoubleAlert($parsedAlert);

					}
					$count++;

					if ($count % 30 === 0)
					{
						$em->flush();
						echo "We're flushing this batch. The count is: ".$count ."<br> ";

					}


				}

			}
			echo "All done, doing last flush<br>";
			$em->flush();
			echo "Last flush done. We're finished with e-mails<br>";


		}
		//end if $emails

	}

	/**
	 * Input information that comes with any basic alert.
	 *  Content, timestamp, newsletter and what type of alert it is.
	 *
	 * By default, do not set alert to approved. Unless it's a profitly alert,we need more work done to it.
	 * By default, set stock to the holder stock - now passed to the function
	 *
	 *
	 * @param \Zezda\AlertBundle\Entity\Newsletter $newsletter
	 * @param array                                $body
	 * @param \Datetime                            $datetime
	 *
	 * @param  \Zezda\ExchangeBundle\Entity\Stock  $stock
	 *
	 * @return \Zezda\AlertBundle\Entity\Alert Alert
	 *
	 */
	private function parseSmsAlertGeneral($newsletter, $body, $datetime, $stock)
	{
		$em = $this->getDoctrine()->getManager();
		$type = $em->getRepository('ZezdaAlertBundle:AlertType')
			->findOneByType('SMS');
		$alert = new Alert();
		$content = new AlertContent();
		$content->setContent($body);
		$alert->setContent($content);
		$alert->setNewsletter($newsletter);
		$alert->setDatetime($datetime);
		$alert->setType($type);
		$alert->setApproved(FALSE);
		$alert->setShort(FALSE);
		$alert->setBuy(TRUE);
		$alert->setPrice(0.00);
		$alert->setShares(0);
		$alert->setStock($stock);

		return $alert;


	}

	/**
	 * @param \Zezda\AlertBundle\Entity\Alert    $alert
	 * @param string                             $message
	 * @param string                             $date
	 * @param \Zezda\ExchangeBundle\Entity\Stock $stock
	 *
	 * @return \Zezda\AlertBundle\Entity\Alert
	 */
	private function parseEmailGeneral($alert, $message, $date, $stock)
	{
		//Set up EM
		$em = $this->getDoctrine()->getManager();

		//Any alert from here is going to have the same type - Email
		$type = $em->getRepository('ZezdaAlertBundle:AlertType')
			->findOneByType('Email');

		//Set the datetime from this goofy format
		$datetime = new \Datetime();
		//$datetime = date_create_from_format("D, j M Y H:i:s", trim(substr($date, 0, -11)));
		$timestamp = strtotime($date);
		//echo $timestamp;
		$datetime->setTimestamp($timestamp);

		//get rid of all the newlines
		$emailMessage = str_replace(array("\n", "\r"), ' ', $message);
		$emailMessage = str_replace("  ", " ", $emailMessage);

		$content = new AlertContent();
		$content->setContent(trim($emailMessage));
		$alert->setContent($content);
		$alert->setType($type);
		$alert->setDatetime($datetime);
		$alert->setApproved(FALSE);
		$alert->setShort(FALSE);
		$alert->setBuy(TRUE);
		$alert->setPrice(0.00);
		$alert->setShares(0);
		$alert->setStock($stock);

		// echo "datetime object:".$datetime->format('Ymd H:i:s'). "date from email:".$date." datetime alert: ".$alert->getDatetime()->format("Ymd H:i")."<br>";
		//unset($datetime);

		return $alert;
	}

	/**
	 * @param \Zezda\AlertBundle\Entity\Alert $alert
	 * @param array                           $newsletters
	 *
	 * newsletter array is of \Zezda\AlertBundle\Entity\Newsletter
	 *
	 * @return mixed
	 * return alert if true, false if it's a watchlist
	 */
	private function parseEmailAlertProfitly($alert, $newsletters)
	{


		$crawler = new Crawler($alert->getContent()->getContent());
		$first = explode(":", trim($crawler->filter('td [sectionid="body"]')->text()), 2);

		//true is an alert, false is watchlist
		if ($this->isProfitlyWatchlistOrAlert($first, $newsletters))
		{
			//The first word is their username, we can remove that leaving just the alert text
			$content = explode(" ", trim($first[1]), 2);
			//Set the content
			$alert->getContent()->setContent(trim($content[1]));

			$parsedProfitlyAlert = $this->parseAlertProfitly($alert);

			return $parsedProfitlyAlert;

			//echo "from: " . $alert->getNewsletter()->getName() . "  |  message: " . $parsedProfitlyAlert->getContent()->getContent() . " for stock: " . $parsedProfitlyAlert->getStock() . " at price: " . $parsedProfitlyAlert->getPrice() . "<br><br>";
		}
		else
		{
			return FALSE;

		}

	}

	/**
	 * @todo: remove the explodes of \n, they're not needed anymore
	 *  Insert information into the alert that we know comes with a profitly alert
	 *  Stock, buy/sell, long/short, alert price, and transaction amount
	 *
	 * Set profitly alerts approved because we know all the information we're going to need.
	 *
	 * @param \Zezda\AlertBundle\Entity\Alert $alert
	 *
	 * @return \Zezda\AlertBundle\Entity\Alert Alert
	 *
	 */


	private function parseAlertProfitly($alert)
	{
		$em = $this->getDoctrine()->getManager();


		//$content = str_replace(array("\n", "\r", "\r\n"), ' ', trim($alert->getcontent()->getContent()));
		//$content = str_replace("  ", " ", $content);

		$arr = explode(' ', trim($alert->getContent()->getContent()));
		$start = $arr[0];
		$approved = TRUE;
		if ($start == "Added" || $start == "Bought" || $start == "Shorted")
		{
			$isBuy = TRUE;
		}
		elseif ($start == "Sold" || $start == "Covered")
		{
			$isBuy = FALSE;
		}
		else
		{
			$ticker = "comment";
			$approved = FALSE;
		}

		if ($start == "Bought")
		{
			$ticker = $arr[2];
			$price = explode("\n", $arr[3], 2);
			$his = substr($price[0], 1);
			$isShort = FALSE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Added" && $arr[4] == "short")
		{
			$ticker = $arr[3];
			$price = explode("\n", $arr[6], 2);
			$his = substr($price[0], 1, -1);
			$isShort = TRUE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Covered" && $arr[2] == "of")
		{
			$ticker = $arr[3];
			$price = explode("\n", $arr[3], 2);
			$his = substr($price[0], 1, -1);
			$isShort = TRUE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Added")
		{
			$ticker = $arr[3];
			$price = explode("\n", $arr[6], 2);
			$his = substr($price[0], 1, -1);
			$isShort = FALSE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Sold" && $arr[2] == "of")
		{
			$ticker = $arr[3];
			$price = explode("\n", $arr[5], 2);
			$his = substr($price[0], 1, -1);
			$isShort = FALSE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Shorted")
		{
			$ticker = $arr[2];
			$price = explode("\n", $arr[3], 2);
			$his = substr($price[0], 1);
			$isShort = TRUE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ($start == "Sold")
		{
			$ticker = $arr[2];
			$price = explode("\n", $arr[3], 2);
			$his = substr($price[0], 1);
			$isShort = FALSE;
			$size = $this->convertAmount($arr[1]);
		}
		elseif ("Covered" == $start)
		{
			$ticker = $arr[2];
			$price = explode("\n", $arr[3], 2);
			$his = substr($price[0], 1);
			$isShort = TRUE;
			$size = $this->convertAmount($arr[1]);
		}
		else
		{
			$isBuy = FALSE;
			$isShort = FALSE;
			$size = 0;
			$his = 0;
			$ticker = "comment";
			$approved = FALSE;
		}
		$stock = $em->getRepository("ZezdaExchangeBundle:Stock")
			->findOneByTicker($ticker);


		$alert->setStock($stock);
		$alert->setBuy($isBuy);
		$alert->setShort($isShort);
		$alert->setShares($size);
		$alert->setPrice($his);
		$alert->setApproved($approved);


		return $alert;


	}

	/**
	 * Check to see if the email is from an alert source.
	 *    If it is from an alerting source, create an alert and set the newsletter.
	 *
	 * @param string $emailFrom
	 * @param array  $newsletters
	 * newsletter array is always of type: \Zezda\AlertBundle\Entity\Newsletter
	 *
	 * @return mixed
	 *
	 * If it's not an alerting source, return false, otherwise, return alert.
	 */
	private function isEmailAlert($emailFrom, $newsletters)
	{
		$alert = new Alert();

		foreach ($newsletters as $newsletter)
		{
			if (strpos ($emailFrom, $newsletter->getEmailFromField()) !== FALSE)
			{
				$alert->setNewsletter($newsletter);

				return $alert;
			}
		}

		return FALSE;


	}

	/**
	 * @param \Zezda\AlertBundle\Entity\Alert $alert
	 *
	 * @return \Zezda\AlertBundle\Entity\Watchlist
	 */

	private function createWatchlist($alert)
	{
		$watchlist = new Watchlist();
		$watchlist->setDatetime($alert->getDatetime());
		$watchlist->setNewsletter($alert->getNewsletter());
		$watchlist->setContent($alert->getContent()->getContent());

		unset($alert);

		return $watchlist;

	}

	/**
	 * @param string $content
	 * @param array  $newsletters
	 *
	 * newsletters is an array of \Zezda\AlertBundle\Entity\Newsletter
	 *
	 * @return bool
	 */
	private function isProfitlyWatchlistOrAlert($content, $newsletters)
	{
		foreach ($newsletters as $newsletter)
		{
			//echo $content[0] . "<br>";
			if ($newsletter->getSmsPrefix() == trim($content[0]))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param \Zezda\AlertBundle\Entity\Alert $alert
	 */
	private function checkDoubleAlert($alert)
	{
		$em = $this->getDoctrine()->getManager();
		$test = $em->getRepository('ZezdaAlertBundle:Alert')
			->findOneBy(array(
				'datetime'   => $alert->getDatetime(), //->format('Y-m-d H:i'),
				'type'       => $alert->getType(),
				'stock'      => $alert->getStock(),
				'newsletter' => $alert->getNewsletter(),

			));
		if (empty($test))
		{
			$em->persist($alert);
		}
	}

	private function checkDoubleWatchlist($watchlist)
	{
		$em = $this->getDoctrine()->getManager();

		$test = $em->getRepository('ZezdaAlertBundle:Watchlist')
			->findOneBy(array(
				'datetime'   => $watchlist->getDatetime(),
				'content'    => $watchlist->getContent(),
				'newsletter' => $watchlist->getNewsletter(),
			));

		if (empty($test))
		{
			$em->persist($watchlist);
		}
	}

	private function convertAmount($size)
	{
		//If the size ends in k, we're dealing with thousands
		if (substr($size, -1) == "k")
		{
			$count = substr($size, 0, -1);
			$count *= 1000;

			return $count;
		}

		return $size;
	}

}
