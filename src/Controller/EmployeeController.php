<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Employes;
use App\Form\EmployeFormType;
use App\Form\EmployeTypeFormType;
use App\Form\StudentTypeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeeController extends AbstractController
{
    private $em;
    private $repo;
    function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Employes::class);
    }

    #[Route('/Dash', name: 'Dash')]
    public function index(): Response
    {        
        return $this->render('Dash.html.twig', []);
    }

    #[Route('/GetEmployee', name: 'GetEmployee')]
    public function GetAllEmployee(Request $request): Response
    {
        $Employee = new Employes();
        $form = $this->createForm(EmployeTypeFormType::class,$Employee);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $newEmployee = $form->getData();
            $this->repo->AddEmployeAsUser($newEmployee);
            $this->em->flush();
            return $this->redirectToRoute('GetEmployee');
        }
        
        $Employes = $this->repo->findAll();
        
        return $this->render('employee/index.html.twig', [
            'form' => $form->createView(),
            'Employes' => $Employes,
        ]);
    }

    #[Route('/PutEmployee/{Id}', name: 'PutEmployee')]
    public function PutStudent(Request $request,$Id): Response
    {
        $Employee =  $this->repo->findOneBy(['id'=>$Id]);
        
        $form = $this->createForm(EmployeTypeFormType::class,$Employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $Employee = $form->getData();
            $this->em->flush();
            return $this->redirectToRoute('GetEmployee');
        }
        
        return $this->render('employee/editEmployee.html.twig', [
            'form'=> $form->createView(),
        ]);
    }


    #[Route('/DeletedEmployee/{Id}', name: 'DeletedEmployee')]
    public function DeletedClasses($Id): Response
    {
        $Employee =  $this->repo->findOneBy(['id'=>$Id]);
        $this->repo->remove($Employee);
        $this->em->flush();
        return $this->redirectToRoute('GetEmployee');
    }
}
