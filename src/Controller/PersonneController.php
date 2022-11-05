<?php

namespace App\Controller;

use App\Entity\Personne;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personnes/stats/{min}/{max}', name: 'personne.stats.ByAge')]
    public function statPerson(ManagerRegistry $doctrine, $min, $max) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        $stats = $mg->statsPersonByAgeIntervale($min,$max);
        if(!isset($stats))
        {
            $this->addFlash('error',"il n'ya a pas de state");
            return $this->redirectToRoute('personne.get');
        }

        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=>$min,
            'ageMax'=>$max]);
    }

    #[Route('/personnes/findByAge/{min}/{max}', name: 'personne.find.ByAge')]
    public function findPersonByAge(ManagerRegistry $doctrine, $min, $max) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        $finded = $mg->findByPersonByAge($min,$max);
        return $this->render('personne/index.html.twig',['personnes'=>$finded]);

    }

    #[Route('/personnes/update/{id}/{fn}/{ln}/{age}', name: 'personne.update')]
    public function updatePersonne(ManagerRegistry $doctrine, Personne $personne = null, $fn,$ln,$age) : Response
    {
        if(isset($personne))
        {
            $mg = $doctrine->getManager();
            $personne->setFirstname($fn);
            $personne->setLastname($ln);
            $personne->setAge($age);

            $mg->persist($personne);


            $this->addFlash('success',$personne->getFirstname()." à été mis a jour");
            //exécution
            $mg->flush();
        }
        else
        {
            $this->addFlash('error',"la personne n'existe pas");
        }
        return $this->redirectToRoute('personne.pagination');
    }

    #[Route('/personnes/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null) : Response
    {
        if(isset($personne))
        {
            $mg = $doctrine->getManager();
            $mg->remove($personne);

            $this->addFlash('success',$personne->getFirstname()." à été supprimer");
            //exécution
            $mg->flush();
        }
        else
        {
            $this->addFlash('error',"la personne n'existe pas");
        }
        return $this->redirectToRoute('personne.pagination');
    }

    #[Route('/personnes/page/{page?1}/{nbrRecord?10}', name: 'personne.pagination')]
    public function pagination(ManagerRegistry $doctrine, $page,$nbrRecord) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        //page 1 : 0-10
        //page 2 : 10-20
        //page 3 : 20-30
        //$finded = $mg->findBy([],['id'=>'ASC'],$nbr,(($page-1)*$nbr));
        //if(!isset($finded))
        // {
        //$this->addFlash('error',"la personne n'existe pas");
        // return $this->redirectToRoute('personne.get');
        // }
        $count = $mg->count([]);
        $nbpagetotale = floor($count/$nbrRecord);
        $per = $mg->findBy([],['id'=>'ASC'],$nbrRecord,(($page-1)*$nbrRecord));
        return $this->render('personne/index.html.twig', [
            'isPaginated'=>true,
            'pageactuelle'=>$page,
            'nbpagetotale' => $nbpagetotale,
            'personnes'=>$per,
            'nbrecords'=>$nbrRecord
        ]);
    }

    #[Route('/personnes/find/{name}', name: 'personne.find')]
    public function FindPerson(ManagerRegistry $doctrine, $name) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        $finded = $mg->findBy(['firstname'=>$name]);
        if(!isset($finded))
        {
            $this->addFlash('error',"la personne n'existe pas");
            return $this->redirectToRoute('personne.get');
        }

        return $this->render('personne/index.html.twig', ['personnes' => $finded]);
    }

    #[Route('/personnes/get', name: 'personne.get')]
    public function getPersonnes(ManagerRegistry $doctrine) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        $personnes = $mg->findAll();

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    // PARAM CONVERTER :
    //  -Object (Personne) dans la paramètre de la fonction
    //  -id de l'objet dans la route
    // elle retourne l'objet cherché
    // il faut donnée une valeur par défaut null sinn elle va déclenchée nullPointerException (il faut déclenché notre propre erreur)
    #[Route('/personnes/get/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null)
    {
        if(!isset($personne))
        {
            $this->addFlash('error',"la personne n'existe pas");
            return $this->redirectToRoute('personne.get');
        }
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/personnes/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine) : Response
    {
        //
        $entityManager = $doctrine->getManager();
        $personne = new Personne();
        $personne->setFirstname("med amine");
        $personne->setLastname("chebbi");
        $personne->setAge(24);

        // tell Doctrine you want to (eventually) save the person (no queries yet)
        $entityManager->persist($personne);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        $this->addFlash('success',"personne ajouté");
        return $this->redirectToRoute('personne.get');
    }


    //    #[Route('/personnes/get/{id<\d+>}', name: 'personne.detail')]
    //    public function detail(ManagerRegistry $doctrine, $id)
    //    {
    //        $mg = $doctrine->getRepository(Personne::class);
    //        $personne = $mg->find($id);
    //        if(!isset($personne))
    //        {
    //            $this->addFlash('error',"la personne n'existe pas");
    //            return $this->redirectToRoute('personne.get');
    //        }
    //        return $this->render('personne/detail.html.twig', [
    //            'personne' => $personne,
    //        ]);
    //    }
}
