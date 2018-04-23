<?php

namespace VoitureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use VoitureBundle\Entity\Marque;
use VoitureBundle\Entity\Voiture;
use  Symfony\Component\Form\Extension\Core\Type\TextType;
use  Symfony\Component\Form\Extension\Core\Type\DateType;
use  Symfony\Bridge\Doctrine\Form\Type\EntityType;
use  Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VoitureController extends Controller
{
/**
*@Route("/addMarque/{nom}")
*/
    public  function  addMarqueAction($nom)
    {
        $m=  new  Marque();
        $m->setNomMarque($nom);
        $em=$this->getDoctrine()->getManager();
        $em->persist($m);
        $em->flush();
        return  $this->render('VoitureBundle:Default:addMarque.html.twig',array('marque'  =>  $m));
    }
    /**
    *@Route("/addVoiture/")
    */
    public  function  addVoitureAction(Request  $request)
    {
    $v=new  Voiture();
    //générer  le  formulaire
    $form=$this->createFormBuilder($v)
            ->add('numSerie',TextType::class)
            ->add('dateMiseCircu',DateType::class)
            ->add('photo',FileType::class,array('label'=>'photo'))
            ->add('marque',EntityType::class,array  ('class'  =>  'VoitureBundle:Marque','choice_label'  =>  'nomMarque','choice_value'  =>'id'))
            ->add('Add',SubmitType::class)->getForm();
    $form->handleRequest($request);
            //tester  si  le  formuaire  est  valide
            if($form->isValid())
            {
                $file = $v->getPhoto();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->container->getParameter('brochures_directory'),
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $v->setPhoto($fileName);
                $em=$this->getDoctrine()->getManager();
                $em->persist($v);
                $em->flush();
            }
            return  $this->render('VoitureBundle:Default:formvoiture.html.twig',array('f'  =>  $form->createView()));
    }
    /**
    *@Route("/listeVoitures",  name="listeV")
    */
    public  function  listeVoitureAction()
    {
        $voitures  =  $this->getDoctrine()->getRepository("VoitureBundle:Voiture")->findAll();
        return  $this->render('VoitureBundle:Default:listevoiture.html.twig',array('voitures'  =>  $voitures));
    }
}