<?php

namespace Bundle\AppBundle\Controller\Admin;


use Bundle\AppBundle\Entity\Category;
use Bundle\AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    public function indexAction()
    {
        $categoriesList = $this->getDoctrine()
                ->getRepository('BundleAppBundle:Category')
                ->findAll();

        return $this->render('BundleAppBundle:Admin/Category:home.html.twig',array(
            'CategoriesList' =>$categoriesList
        ));

    } 
    public function createAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(new CategoryType(), $category);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->saveCategory($category);
                return $this->redirect($this->generateUrl('category_list'));
            }
        }

        return $this->render(
            'BundleAppBundle:Admin/Category:form.html.twig',
            array(
                'form'     => $form->createView()
            )
        );
        
    }
    public  function saveCategory(Category $category) {

        $category->setCreatedDate(new \DateTime(date('Y-m-d')));
        $category->setCreatedBy($this->getUser());
        $category->setStatus('Active');
        $categoryRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Category");
        $categoryRepo->create($category);
    }

    public function changeStatusAction(Category $category){


        if($category->getStatus() === 'Active'){
            $category->setStatus('Inactive');

            $massage = 'Category Successfully Not Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        } else{
            $category->setStatus('Active');

            $massage = 'Category Successfully Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        }
        $this->getDoctrine()->getRepository('BundleAppBundle:Category')->persist($category);

        return $this->redirect($this->generateUrl('category_list'));
    }
    
}
