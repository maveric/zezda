<?php
/*
Controller to pull candles from available sources so we can keep a record in our own local database
for the purpose of looking up alert stocks at any time in the past
*/

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
}
?>
