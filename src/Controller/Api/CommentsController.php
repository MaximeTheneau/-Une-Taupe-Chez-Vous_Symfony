<?php

namespace App\Controller\Api;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Repository\CommentsRepository;
use App\Repository\PostsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/comments")
 */
class CommentsController extends ApiController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    /**
    * @Route("", name="add_comments", methods={"POST"})
    */
    public function add(Request $request, MailerInterface $mailer, PostsRepository $postsRepository, EntityManagerInterface $entityManager): JsonResponse
    {    


        $content = $request->getContent();
        $data = json_decode($content, true);

        $post = $postsRepository->findOneBy(['slug' => $data['posts']]);

        $comment = new Comments();
        $comment->setUser($data['user']); // Remplacez 'user' par le nom de votre champ
        $comment->setEmail($data['email']); // Remplacez 'email' par le nom de votre champ
        $comment->setComment($data['comment']); // Remplacez 'comment' par le nom de votre champ
        $comment->setAccepted(false); // Par défaut, non accepté
    
        $post->addComment($comment); // Supposons que vous avez une méthode addComment dans l'entité Posts pour gérer la relation
    
        // Persistez les données dans la base de données
        $entityManager->persist($post);
        $entityManager->persist($comment);
        $entityManager->flush();
        


        // if (empty($data['user']) || empty($data['email']) || empty($data['comment']) || empty($data['posts']) || empty($data['accepted'] )) {
        //     return $this->json(
        //         [
        //             "erreur" => "Erreur de saisie",
        //             "code_error" => 404
        //         ],
        //         Response::HTTP_NOT_FOUND, // 404
        //     );
        // }

        // if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     return $this->json(
        //         [
        //             "erreur" => "Adresse e-mail invalide",
        //             "code_error" => 40
        //         ],
        //         Response::HTTP_BAD_REQUEST, // 400
        //     );
        // }

        $email = (new TemplatedEmail())
            ->to($_ENV['MAILER_TO_WEBMASTER'])
            ->from($_ENV['MAILER_TO'])
            ->subject('Nouveau Commentaire de ' . $data['user'] )
            ->htmlTemplate('emails/comments.html.twig')
            ->context([
                'user' => $data['user'],
                'emailUser' => $data['email'],
                'comment' => $data['comment'],
                'posts' => $data['posts'],
                'accepted' => $data['accepted'],
                'id' => $comment->getId(),
            ])
            ->replyTo($data['email']);

        $mailer->send($email);

        return $this->json(
            [
                "message" => "Votre commentaire a bien été envoyé ! On le valide au plus vite !",
            ],
            Response::HTTP_OK,
        );

        
        }

    /**
     * @Route("/delete/{id}", name="admin_comment_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, CommentsRepository $CommentsRepository): Response
    {

        if ($request->isMethod('GET')) {
            $comment = $CommentsRepository->find($request->get('id'));
            $this->entityManager->remove($comment);
            $this->entityManager->flush();

            return new RedirectResponse('https://unetaupechezvous.fr/');

        }
    }

    /**
     * @Route("/validate/{id}", name="admin_comment_validate", methods={"GET", "POST"})
     */
    public function validate(Request $request, CommentsRepository $CommentsRepository ): JsonResponse
    {

        if ($request->isMethod('GET')) {
            dd($request);
            $comment = $CommentsRepository->find($request->get('id'));
            $comment->setAccepted(true);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        

            return $this->json(
                [
                    "accepted" => $comment->isAccepted(),
                    "message" => "Commentaire validé",

                ],
                Response::HTTP_OK,
            );

        } 

    }


}