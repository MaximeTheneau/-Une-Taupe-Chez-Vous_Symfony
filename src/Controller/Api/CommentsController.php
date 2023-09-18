<?php

namespace App\Controller\Api;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Entity\User;
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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/api/comments")
 */
class CommentsController extends ApiController
{
    private $entityManager;
    private $passwordHasher;
    private $jwtManager;
    private $csrfTokenManager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager, 
        JWTTokenManagerInterface $jwtManager, 
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->csrfTokenManager = $csrfTokenManager;
    }
    /**
    * @Route("", name="add_comments", methods={"POST"})
    */
    public function add(Request $request, MailerInterface $mailer, PostsRepository $postsRepository): JsonResponse
    {    
        
        $content = $request->getContent();
        $data = json_decode($content, true);

        $post = $postsRepository->findOneBy(['slug' => $data['posts']]);

        $comment = new Comments();
        $comment->setUser($data['user']); 
        $comment->setEmail($data['email']); 
        $comment->setComment($data['comment']); 
        $comment->setAccepted(false);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $post->addComment($comment); 
    

        $this->entityManager->persist($post);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        


        // if (empty($data['user']) || empty($data['email']) || empty($data['comment']) || empty($data['posts'])  ) {
        //     return $this->json(
        //         [
        //             "erreur" => "Erreur de saisie",
        //             "code_error" => 400
        //         ],
        //         Response::HTTP_NOT_FOUND, // 400
        //     );
        // }

        // if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     return $this->json(
        //         [
        //             "erreur" => "Adresse e-mail invalide",
        //             "code_error" => 400
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

    /**
     * @Route("/verify_email", name="verify", methods={"POST"})
     */
    public function verifyEmail(Request $request, HttpClientInterface $httpClient, EntityManagerInterface $entityManager): JsonResponse
    {
    $donneesRequete = json_decode($request->getContent(), true);
    $email = $donneesRequete['email'] ?? null;
    if ($email) {

        $urlAPI = 'https://api.mailcheck.ai/email/' . $email;
        
        try {
            $reponse = $httpClient->request('GET', $urlAPI);
            $donnees = $reponse->toArray();
            
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if ($existingUser) {
                return new JsonResponse(['message' => true]);
            }

                if ($donnees['disposable']) {
                    return new JsonResponse(['message' => 'L\'e-mail est jetable et n\'est pas accepté.'], 400);
                } elseif (!$donnees['mx']) {
                    return new JsonResponse(['message' => 'L\'e-mail est invalide.'], 400);
                } else {

                    $currentDate = new \DateTimeImmutable();
                    $password = $currentDate->format('Ymd');
                    $password .= random_int(1000, 9999);

                    $user = new User();
                    $user->setEmail($email);
                    $user->setRoles(['ROLE_COMMENT']);
                    $user->setPassword($this->passwordHasher->hashPassword($user, $password ));

                    $entityManager->persist($user);
                    $entityManager->flush();

                    return new JsonResponse(['message' => true]);
                }

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la vérification de l\'e-mail.'], 500);
        }
    }

    return new JsonResponse(['message' => 'L\'e-mail n\'est pas présent ou n\'est pas valide.'], 400);

    }
}