<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\FileUploader;
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
     * @Route("/todo/insert", name="todo_insert")
     */
    public function insertAction(Request $request)
    {
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('file_type', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('file_size', TextType::class, array('attr' => array('class' => 'form-control', 'style' =>'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' =>'margin-bottom:15px')))
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
                'Saved'
            );

            return $this->redirectToRoute('todo_list');
        }    

        return $this->render('todo/insert.html.twig', array(
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
     * @Route("/todo/upload", name="todo_upload")
     */
    public function uploadAction(Request $request, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();

        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $todo->getSample();
            $fileName = $fileUploader->upload($file);

            $todo->setSample($fileName);

            // Update the 'sample' property to store the PDF file name
            // instead of its contents
            $todo->setSample($fileName);

            // ... persist the $todo variable or any other work

            return $this->redirect($this->generateUrl('todo_list'));
        }

        return $this->render('todo/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }  

      /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {

        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
            
        return $this->render('todo/details.html.twig', array(
            'todo' => $todo
        ));

        return $this->render('todo/details.html.twig');
    }
}