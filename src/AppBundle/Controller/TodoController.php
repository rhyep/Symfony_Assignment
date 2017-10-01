<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findAll();

        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));
    }

      /**
     * @Route("/todo/upload", name="todo_upload")
     */
    public function uploadAction(Request $request)
    {
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('file_type', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('file_size', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Upload', 'attr' => array('class' => 'btn btn-primary', 'style' =>'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //Upload Data to databse
            $name = $form['name']->getData();
            $file_type = $form['file_type']->getData();
            $file_size = $form['file_size']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setUpdatedDate($now);
            $todo->setCreatedDate($now);
            $todo->setFileType($file_type);
            $todo->setFileSize($file_size);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice',
                'File Uploaded'
            );

            return $this->redirectToRoute('todo_list');
        }    

        return $this->render('todo/upload.html.twig', array(
            'form' => $form->createView()
        ));
    }

      /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        return $this->render('todo/edit.html.twig');
    }

      /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {
        return $this->render('todo/details.html.twig');
    }
}