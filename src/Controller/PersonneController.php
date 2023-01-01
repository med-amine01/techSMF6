<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\MailerService;
use App\Service\SystemUserService;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[
    Route("/personne"),
    IsGranted("ROLE_USER")
]
class PersonneController extends AbstractController
{

    public function __construct(private SystemUserService $userService){}

    #[Route('/personnes/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine,
                                Request $request,
                                MailerService $mailer) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $personne = new Personne();

        //l'image de notre formulaire
        $form = $this->createForm(PersonneType::class, $personne);

        $form->handleRequest($request); //form now know it's a post request


        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();
            //setfield created By
            $personne->setCreatedBy($this->getUser());
            //$form->getData(); return array of data
            $entityManager->persist($personne);
            $entityManager->flush();

            $this->addFlash('success',"la personne a été ajouté avec succée");

            $mailMessage =
                $personne->getFirstname().' '.
                $personne->getLastname(). 'est ajouté avec succée à '.
                $personne->getCreatedAt()->format('d-m-Y');

            $mailer->sendEmail(content: $mailMessage);

            return $this->redirectToRoute('personne.pagination');
        }
        return $this->render('personne/add-personne.html.twig',[
            //passer la form au formulaire
            'f'=>$form->createView(),
            'isEdited'=>false
        ]);
    }

    #[Route('/personnes/update/{id?0}', name: 'personne.update')]
    public function updatePersonne(ManagerRegistry $doctrine,
                                   Personne $personne = null,
                                   Request $request,
                                   MailerService $mailer) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //si la personne (id ==0) est null
        if (!isset($personne))
        {
            return $this->redirectToRoute('personne.pagination');
        }

        $form = $this->createForm(PersonneType::class, $personne);

        $form->handleRequest($request); //form now know it's a post request

        if ($form->isSubmitted())
        {
            $entityManager = $doctrine->getManager();
            //$form->getData(); return array of data

            $entityManager->persist($personne);
            $personne->setCreatedBy($this->getUser());
            $entityManager->flush();
            $message =
                $personne->getFirstname().' '.
                $personne->getLastname().' a été mis a jour à '.
                $personne->getUpdatedAt()->format('d-m-Y');


            $mailer->sendEmail(
                from:'mad.chicken211@gmail.com',
                to: 'testing.symfony123@gmail.com',
                content: $message,
                subject: "suppression d'une personne",
                personne: $personne);

            $this->addFlash('success', "la personne a été mis a jour");

            return $this->redirectToRoute('personne.pagination');
        }

        return $this->render('personne/add-personne.html.twig',[
            //passer la form au formulaire
            'f'=>$form->createView(),
            'isEdited'=>true
        ]);
    }

    #[Route('/personnes/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(ManagerRegistry $doctrine,
                                   Personne $personne = null) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
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

    #[Route('/personnes/page/{page?1}/{nbrRecord?10}', name: 'personne.pagination')]
    public function pagination(ManagerRegistry $doctrine, $page,$nbrRecord) : Response
    {
        $mg = $doctrine->getRepository(Personne::class);
        /**
           page 1 : 0-10
            page 2 : 10-20
            page 3 : 20-30
            $finded = $mg->findBy([],['id'=>'ASC'],$nbr,(($page-1)*$nbr));
            if(!isset($finded))
            {
            $this->addFlash('error',"la personne n'existe pas");
            return $this->redirectToRoute('personne.get');
            }
         */
        $count = $mg->count([]);
        $nbpagetotale = ceil($count/$nbrRecord);
        $per = $mg->findBy([],['id'=>'ASC'],$nbrRecord,(($page-1)*$nbrRecord));
        return $this->render('personne/index.html.twig', [
            'isPaginated'=>true,
            'pageactuelle'=>$page,
            'nbpagetotale' => $nbpagetotale,
            'personnes'=>$per,
            'nbrecords'=>$nbrRecord
        ]);
    }








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

//    #[Route('/personnes/get', name: 'personne.get')]
//    public function getPersonnes(ManagerRegistry $doctrine) : Response
//    {
//        $mg = $doctrine->getRepository(Personne::class);
//        $personnes = $mg->findAll();
//
//        return $this->render('personne/index.html.twig', [
//            'personnes' => $personnes,
//        ]);
//    }



}
