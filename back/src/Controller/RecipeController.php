<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    //Toute les méthode dans le controleur doivent retourner une reponse !!
    #[Route(path: '/recette', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $recipeRepository, /*EntityManagerInterface $entityManager*/): Response
    {
        //La methode appeller ici pour avoir toute les recettes sont dans le Repository "RecipeRepository.php"
        //C est dedans ou il y auras toute les commande SQL pour crée des méthode pour retourné ajouter supprimer des donner 
        $recipes = $recipeRepository->findAll();
        $nbTotalRecettes = $recipeRepository->nbTotalRecette();

        // C est comme ça que l'on crée une nouvelle entité que l'on l'ajoute a la base de données
        //avec un persist() qui garde l'objet en vie pour conserver l'objet et l ajouter a la base avec un flush()
        // $recipe = new Recipe();
        // $recipe->setTitle("Une recette de fous malade")
        //     ->setText("bah voila enfait tu met juste ta génialitude dedans")
        //     ->setSlug("Une_recette_de_fous_malade")
        //     ->setDuration(40)
        //     ->setCreatedAt(new \DateTimeImmutable())
        //     ->setUpdatedAt(new \DateTimeImmutable());
        // $entityManager->persist($recipe);
        // $entityManager->flush();
        //Pour retirer un objet de la bdd
        // $entityManager->remove($recipe[0]);
        // $entityManager->flush();

        //dd($entityManager->getRepository(Recipe::class)->findAll());

        //Mettre a jour les information des entité dans la base de données
        // $recipes[0]->setTitle('Pâtes bolognaisuuuu');
        // $entityManager->flush();

        //dd($recipes);
        return $this->render(view: 'recipe/index.html.twig', parameters: [
            'recipes' => $recipes,
            'nbTotalRecettes' => $nbTotalRecettes
        ]);
    }

    #[Route(path: '/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]// le Requirements sert a trier la chaine(si cest des charactère, chiffre, etc...)
    public function show(Request $request, string $slug, int $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);
        if ($recipe->getSlug()!= $slug){ 
            return $this->redirectToRoute('recipe.show', parameters: [
                'slug'=> $recipe->getSlug(),
            ]);
        }
        return $this->render(view: 'recipe/show.html.twig', parameters: [
            'recipe' => $recipe
        ]);
    }

    #[Route(path: '/recettes/{id}/edite',name: 'recipe.edite',requirements: ['id' => '\d+'])]
    /*L'aventage avec symfony c est qu'on peut juste rensigner l'objet et il vas trouver tout seul qu'elle objet fait référence au param*/
    public function edite(Recipe $recipe, Request $request,EntityManagerInterface $em)
    {
        // Crée le form en lui renseigant le forme qu'on a crée et les données pour
        $form = $this->createForm(RecipeType::class, $recipe);
        //vas modifier tout seul l entité avec la requètes suivante 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            // le addFlash permet d'afficher un message a la redirection en l'occurence il s'affiche a la redirection pour vérifier
            // si le changement est bon
            $this->addFlash('success','la recette ');
            return $this->redirectToRoute('recipe.index');
        } 
        return $this->render('recipe/edite.html.twig',[
            'recipe'=> $recipe,
            'form' => $form
        ]);
    }
}
