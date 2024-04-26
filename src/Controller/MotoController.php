<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MotoController extends AbstractController
{
    #[Route('/',name: 'liste_moto')]
    public function index(Request $request, EntityManagerInterface $em): Response
     {  
        $motos= $em->getRepository(Moto::class)->findAll();
        $em->flush();
        return $this->render('moto/index.html.twig',[
            'motos'=>$motos
        ]);
    }

    #[Route('/moto/{id}/details', name: 'details', requirements : ['id'=> '\d+'])]
    public function show(Request $request,MotoRepository $repository, int $id): Response{
    $moto = $repository->find($id);

    if($moto->getId() !== $id){
        return $this->redirectToRoute('details', ['id' => $moto->getId()]);
    }
    
       return $this->render('moto/details.html.twig',[
    'moto'=>$moto
    ]);
    }

    #[Route(path : '/moto/create', name : 'creer')]
    public function creer(Request $request, EntityManagerInterface $em) : Response{
        $moto = new Moto;
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em->persist($moto);
            $em->flush();
            $this->addFlash('success','La moto'. $moto->getNom().' a bien été créée');
            return $this->redirectToRoute('liste_moto');
        }
        return $this->render('moto/creer.html.twig',[
            'Form' => $form
        ]);
    }

    #[Route(path : '/moto/{id}/editer', name : 'editer')]
    public function editer(Moto $moto, Request $request, EntityManagerInterface $em) : Response{
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em->flush();
            return $this->redirectToRoute('details',['id'=>$moto->getId()]);
        }
        return $this->render('moto/editer.html.twig',[
            'moto' => $moto,
            'Form' => $form
        ]);
    }
    #[Route(path : '/moto/{id}/supprimer', name : 'supprimer')]
        public function delete(Moto $moto, EntityManagerInterface $em) : Response{
            $nom = $moto->getNom();
            $em->remove($moto);
            $em->flush();
            $this->addFlash('info','La moto'. $nom . ' a bien été supprimée');
                return $this->redirectToRoute('liste_moto');
            }
}
