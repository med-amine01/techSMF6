<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if(!$session->has('todos'))
        {
            $todos = array(
                "nom"=>"symfony",
                "id"=>1,
                "enseingant"=>"ali",
                "nbheurs"=>42,
                "date"=>"12-12-2022"
            );
            $session->set('todos',$todos);
            $this->addFlash('info',"la liste à été initialisée");
        }


        return $this->render('todo/index.html.twig');
    }

    #[Route('/todo/add/{name}/{content}', name: 'addTodo')]
    public function addTodo(Request $request, $name, $content):Response
    {
        $session = $request->getSession();
        if($session->has('todos'))
        {
            $todos = $session->get('todos');
            if(isset($todos[$name]))
            {
                $this->addFlash('error',"le todo déjà existe");
            }
            else
            {
                $todos[$name] = $content;
                $session->set('todos',$todos);
                $this->addFlash('success',"le todo est ajouté");
            }
        }
        else
        {
            $this->addFlash('error',"la liste n'est pas encore initialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/todo/update/{name}/{content}', name: 'updateTodo')]
    public function updateTodo(Request $request, $name, $content):Response
    {
        $session = $request->getSession();
        if($session->has('todos'))
        {
            $todos = $session->get('todos');
            if(isset($todos[$name]))
            {
                $todos[$name] = $content;
                $session->set('todos',$todos);
                $this->addFlash('success',"le todo à été mise à jour");
            }
            else
            {
                $this->addFlash('error',"le todo déjà n'existe pas");
            }
        }
        else
        {
            $this->addFlash('error',"la liste n'est pas encore initialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/todo/delete/{name}', name: 'deleteTodo')]
    public function DeleteTodo(Request $request, $name):Response
    {
        $session = $request->getSession();
        if($session->has('todos'))
        {
            $todos = $session->get('todos');
            if(isset($todos[$name]))
            {
                unset($todos[$name]);
                $session->set('todos',$todos);
                $this->addFlash('success',"le todo à été supprimé");
            }
            else
            {
                $this->addFlash('error',"le todo déjà n'existe pas");
            }
        }
        else
        {
            $this->addFlash('error',"la liste n'est pas encore initialisée");
        }

        return $this->redirectToRoute('todo');
    }


}
