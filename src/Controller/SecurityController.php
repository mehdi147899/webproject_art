<?php

namespace App\Controller;

use Swift_Mailer;
use Swift_Message;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPasswordType;
use App\Service\TokenGenerator;
use App\Form\ForgotPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/Login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/Login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/profile', name: 'app_profile')]
public function profile(Request $request, Security $security, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
{
    // Get the currently logged-in user (make sure it's a User entity)
    $user = $security->getUser();

    // Check if the user is logged in
    if (!$user) {
        throw $this->createAccessDeniedException('You must be logged in to access this page.');
    }

    // If the user is logged in, we cast it to the actual User class
    if (!$user instanceof User) {
        throw $this->createAccessDeniedException('The logged-in user is not valid.');
    }

    // Handle the form submission (image update)
    if ($request->isMethod('POST') && $request->files->get('image')) {
        $image = $request->files->get('image');

        if ($image) {
            // Generate a unique file name based on the original file name
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            // Move the file to the directory where images are stored
            try {
                $image->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Error uploading image');
                return $this->redirectToRoute('app_profile');
            }

            // Set the new image filename to the user's profile
            $user->setImage($newFilename);

            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Image de profil mise à jour avec succès.');
        }
    }

    // Pass the user data to the Twig template
    return $this->render('security/Profile.html.twig', [
        'user' => $user,
    ]);
}

    
}