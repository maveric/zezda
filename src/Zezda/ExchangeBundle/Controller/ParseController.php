<?php

namespace Zezda\ExchangeBundle\Controller;

//Symfony extensions and such
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


//Uses from our own created library
use Zezda\ExchangeBundle\Form\FileUploadType;
use Zezda\ExchangeBundle\Entity\Stock;
use Zezda\ExchangeBundle\Entity\Candle;

class ParseController extends Controller
{

	public $mysqli;

	/**
	 * First page that you see. Used to upload files and set them to parsing.
	 *
	 * @Route("/exchange/index")
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		//Give us some information here.
		$content = "This is the form before it's submitted.";

		//Setup the form for our upload
		$form = $this->createForm(new FileUploadType());

		//Is it currently being submitted?
		$form->handleRequest($request);

		//If the form is vaild
		if ($form->isValid())
		{
			//Setup our varibles with form data
			$file = $form['attachment']->getData();
			$location = $form['location']->getData();
			//I'm the only one submitting here. Not worried about rando's uploading so I use my filename to keep track accuratly
			$file->move($location, $file->getClientOriginalName());

			$filename = $location . $file->getClientOriginalName();

			$this->dataToDatabaseActionEOD($file->getClientOriginalName());
			//Let myself know it worked out fine.
			$content = "This is the page after form submitted.";
			//$content=$filename;
		}
		//If the form is bad
		else
		{
			//if the form is bad and it's a request
			if (!$request)
			{
				$content = "something went wrong";
			}
		}


		/**
		 * create a list of all of the files in the directory
		 * A quick browse will prevent double uploads
		 */

		//Create array for filenames to live in
		$names = array();
		$finder = new Finder();
		$finder->files()->in("/home/zezda/projects/zezda/candles/");
		$finder->sortByName();

		foreach ($finder as $file)
		{
			$names[] = $file->getrelativePathname();
		}


		//Send it out to be rendered.
		return $this->render('ZezdaExchangeBundle:Parse:index.html.twig', array(
			'content' => $content,
			'form'    => $form->createView(),
			'names'   => $names,
		));
	}

	/**
	 * Testing out the google candle pulling abilities
	 * @todo: create datetimes from those first lines.
	 *
	 * @Route("/exchange/google/{ticker}", name="googleCandle")
	 *
	 */
	public function pullGoogleCandles($ticker="GOOG")
	{
		$lines = file("https://www.google.com/finance/getprices?i=60&p=10d&f=d,o,h,l,c,v&df=cpct&auto=1&q=".$ticker);
		$staticTime = new \Datetime("@946684800");
		$holdTime = new \Datetime("@946684800");
		$timestamp = 0;
        //$headers = array('exchange'=>, );

		foreach ($lines as $line)
		{
			$thisLine = explode(",", $line, 2);
			if (strpos($thisLine[0], 'a') === 0)
			{
				$timestamp = (int)substr($thisLine[0], 1) - (300 * 60);
				//$timestamp -= 300*60;
				//echo $timestamp."<br>";
				$holdTime->setTimestamp($timestamp);
				$dt = $holdTime;
				echo $dt->format('Ymd H:i') . " " . $thisLine[1] . "<br>";

			}
			elseif ($staticTime != $holdTime)
			{
				$innerTimestamp = $timestamp + ($thisLine[0] * 60) + 300 * 60;

				$datetime = new \Datetime();
				$datetime->setTimestamp($innerTimestamp);

				echo $datetime->format('Ymd H:i') . " " . $line . "<br>";
			}
			else
			{
				$headers = explode("=", $line);
				echo "fakeline: " . urldecode($line) . "<br>";
			}

		}

	}


	/**
	 * takes in a file path and parses the data in that file.
	 * Filename data is expected in 7 column format
	 *
	 * * @Route("/exchange/convert/{name}", name="dataToDatabase" )
	 */
	public function dataToDatabaseActionEOD($name)
	{
		echo "test";
		GLOBAL $numCandlesFlushed;
		GLOBAL $numCandlesChecked;
		$this->mysqli = new \mysqli('zezda.cnm7x04a2usw.us-east-1.rds.amazonaws.com', 'zezda', 'sch00lsucks', 'zezda', '3306');

		if ($this->mysqli->connect_error)
		{
			die('Connect Error (' . $this->mysqli->connect_errno . ') '
				. $this->mysqli->connect_error);
		}

		/**
		 * Setup the function wide variables.
		 */
		//Setup the EM
		$em = $this->getDoctrine()->getManager();

		//Candles will always be in this directory
		$dir = "/home/zezda/projects/zezda/candles/";

		/**
		 * End variable group
		 */


		//if the filename hasn't been chagned, the file hasn't been parsed and we can do it again.
		if (substr($name, -6) != "parsed")
		{

			//give default data
			$candleTimeStart = microtime(TRUE);
			$timesFlushed = 0;

			//initiate some time-check variables
			$numCandlesChecked = 0;
			$numCandlesFlushed = 0;
			$totalExecutionTime = 0;
			$totalFlushedCandles = 1;
			$totalCheckedCandles = 0;

			//initiate some batch variables
			$batchSize = 30;


			//Setup our file handler
			$handle = fopen($dir . $name, "r");

			//We need the name of the file so we know the exchange it's from
			$nameHoldTemp = explode("_", $name);
			$exchange = $nameHoldTemp[0];

			//initiate stock
			$testStock = new Stock();
			$stock = new Stock();

			//If the handler got set read the file
			if ($handle)
			{
				$em = $this->getDoctrine()->getManager();
				//If there is still more to read, do so.
				while (($line = fgets($handle)) !== FALSE)
				{
					$em = $this->getDoctrine()->getManager();
					//Seperate the data by it's delimiter - the ',' char.
					$info = explode(",", $line);
					//print $info[0].":".$info[1].":".$info[2].":".$info[3].":".$info[4].":".$info[5].":".$info[6]."<br>";

					/**
					 * If the stock is unset (first time through script)or if current stock != last stock, update it.
					 *
					 * get the latest dated candle for this stock after update.
					 *
					 *
					 */
					if ($stock->getTicker() != $info[0])
					{
						$candlesStockTime = microtime(TRUE) - $candleTimeStart;
						$stock = $this->stockExistsOrMakeEOD($info[0], $exchange);
						$latestCandleForStock = $em->getRepository('ZezdaExchangeBundle:Candle')
							->findLatestCandleForStock($stock);
						//print "stock: " .$stock->getTicker(). "<br>";

					}

					//If the stock exists, just add this candle to those of that stock
					if ($stock)
					{
						//Make the candles
						if (\Doctrine\ORM\UnitOfWork::STATE_MANAGED !== $em->getUnitOfWork()->getEntityState($stock))
						{
							$em->merge($stock);
						}
						$this->makeEODCandle($stock, $info, $latestCandleForStock);

						if ($stock->getTicker() != $testStock->getTicker())
						{
							$candleTimeStart = microtime(TRUE);


							$start = microtime(TRUE);
							if ($numCandlesFlushed != 0)
							{
								//$em->flush();
							}

							$flushTime = microtime(TRUE) - $start;
							$totalFlushedCandles += $numCandlesFlushed;
							$totalCheckedCandles += $numCandlesChecked;

							/**
							 * Nice big print statement - shows some nice debugging and timing info.
							 */
							print $testStock->getTicker() . " flushed  - . - . - mem usage at:" . memory_get_usage() . " : " . ($numCandlesFlushed) . " candles flushed in " . number_format($flushTime, 5) . " seconds.   Making/checking " . ($numCandlesChecked) . " candles took: " . number_format($candlesStockTime, 5) . " seconds. This time: " . number_format(($flushTime + $candlesStockTime), 5) . ". Total time: " . number_format(($totalExecutionTime += ($flushTime + $candlesStockTime)), 5) . " Avg flshCndl/sec =" . number_format(($totalFlushedCandles / $totalExecutionTime), 5) . " |  ChkCnld/sec = " . number_format(($numCandlesChecked / $candlesStockTime), 5) . "<br>";
							$numCandlesFlushed = 0;
							$numCandlesChecked = 0;

							$testStock = $stock;
							//$em->clear();
							//unset($em);

						}
					}
					//We need to add the stock to the stock table, then do the same as above.
					else
					{
						print "something went wrong";

					}
				}
				$em->flush();
				print "done flusing";


				//Rename the file so we know not to use it again.

				$fs = new Filesystem();

				try
				{

					$fs->rename($dir . $name, $dir . $name . "parsed");
				}
				catch (IOExceptionInterface $e)
				{
					echo "An error occurred while renaming your file at " . $e->getPath();
				}


			}
			else
			{
				echo "file didn't open.... something happened";
			}

		}


		//The file has already been parsed, and the name of the file has been changed. Don't do it again.
		else
		{
			print "you already parsed that file. Try again boso";

		}

	}

	/**
	 *
	 * @param type $ticker
	 * @param type $exchange
	 *
	 * Check to make sure the stock exists before we create a new one.
	 *
	 * @return \Zezda\ExchangeBundle\Entity\Stock
	 */
	private function stockExistsOrMakeEOD($ticker, $exchange)
	{
		//$stock = new Stock();

		$em = $this->getDoctrine()->getManager();
		$stock = $em->getRepository('ZezdaExchangeBundle:Stock')
			->findOneBy(array(
				'ticker'   => $ticker,
				'exchange' => $exchange,
			));
		if (!$stock)
		{
			$stock = new Stock();
			$stock->setTicker($ticker);
			$stock->setExchange($exchange);

			$em->persist($stock);
			$em->flush();
		}

		/**
		 * If the stock is alraedy in the databse, return the id (object?) otherwise, return 0 (null?)
		 */

		return $stock;
	}

	//Make the candle for the stock
	private function makeEODCandle($stock, $data, $lastCandle)
	{

		//Setup the EM
		$em = $this->getDoctrine()->getManager();

//		convert the datetime to a usable format
		$datetime = date_create_from_format('YmdHi', $data[1]);

//		Update the timekeeping global
		$GLOBALS['numCandlesChecked']++;

		//print "last candle: ".$lastCandle[0]['datetime']->format('Ymd H:i'). " for stock: ". $stock->getTicker()."<br>";

		//if (empty($lastCandle) || $datetime > $lastCandle[0]['datetime'])
		{
			//print "last candle: ".$lastCandle[0]['datetime']->format('Ymd H:i'). " current candle: ".$datetime->format('Ymd H:i') ." for stock: ". $stock->getTicker()."<br>";
			$GLOBALS['numCandlesFlushed']++;
			/*
						$candle = new Candle();
						$candle->setStock($stock);
						$candle->setDatetime($datetime);
						$candle->setOpen($data[2]);
						$candle->setHigh($data[3]);
						$candle->setLow($data[4]);
						$candle->setClose($data[5]);
						$candle->setVolume($data[6]);

						//$em->persist($candle);
						unset($candle);
			*/
			/* if ($GLOBALS['numCandlesFlushed'] % 100 == 0)
				{
					//print "fluahed another 100 of:". $stock->getTicker(). "<br>";
					$em->flush();
					//$em->clear();
				}
			   */
			$stockId = $stock->getId();
			$dtFormat = $datetime->format('Y-m-d H:i:s');
			if ($this->mysqli->query("INSERT INTO Candle (stock, datetime, open, high, low, close, volume) VALUES ('" . $stockId . "', '" . $dtFormat . "', '" . $data[2] . "', '" . $data[3] . "', '" . $data[4] . "', '" . $data[5] . "', '" . $data[6] . "')") !== TRUE)
			{
				echo "your sql failed! says:" . $this->mysqli->error . "<br>";
			}

		}


		return;
	}

}
