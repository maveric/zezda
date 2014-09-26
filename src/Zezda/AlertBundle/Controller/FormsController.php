<?php

namespace Zezda\AlertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

//our made calsses
use Zezda\AlertBundle\Entity\Alert;
use Zezda\AlertBundle\Entity\Newsletter;
use Zezda\AlertBundle\Form\AlertType;
use Zezda\AlertBundle\Form\NewsletterType;

/**
 * Class FormsController
 * @package Zezda\AlertBundle\Controller
 */
class FormsController extends Controller
{
	/**
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @Route("/alert/forms/alert")
	 * @Route("/alert/forms/alert/{id}")
	 * @Template()
	 */
	public function indexAction(Request $request, $id = 0)
	{
		$em = $this->getDoctrine()->getManager();

		$content = "This is a test";
		//Setup the form for our upload
		$newsletter = $em->getRepository('ZezdaAlertBundle:Newsletter')
			->find($id);
		$form = $this->createForm(new AlertType());

		//Is it currently being submitted?
		$form->handleRequest($request);


		return $this->render('ZezdaAlertBundle:Forms:index.html.twig', array(
			'content' => $content,
			'form'    => $form->createView(),
		));

	}

	/**
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @Route("/alert/forms/newsletter")
	 * @Route("/alert/forms/newsletter/{id}", name="newsletterEdit")
	 */

	public function newsletterAction(Request $request, $id = 0)
	{
		$em = $this->getDoctrine()->getManager();
		$content = "this is the newsletter addition file";

		$letter = $em->getRepository('ZezdaAlertBundle:Newsletter')
			->find($id);
		$allNewsletters = $em->getRepository('ZezdaAlertBundle:Newsletter')
			->findAll();

		if ($letter)
		{

			$form = $this->createForm(new NewsletterType(), $letter);
			$content .= " for " . $letter->getName();
		}
		else
		{


			$form = $this->createForm(new NewsletterType());
		}

		$form->handleRequest($request);

		if ($form->isValid())
		{
			$newsletter = $form->getData();
			$em->persist($newsletter);
			$em->flush();
			$content = "We made the datas for: " . $newsletter->getName() . "!";
		}

		return $this->render('ZezdaAlertBundle:Forms:index.html.twig', array(
			'content'     => $content,
			'form'        => $form->createView(),
			'newsletters' => $allNewsletters,
		));
	}
}
