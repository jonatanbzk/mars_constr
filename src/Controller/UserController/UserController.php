<?php


namespace App\Controller\UserController;


use App\Entity\User;
use App\Form\User\UserRegisterType;
use App\Form\User\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface
    $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('first_name')->getData() . '.' .
                $form->get('last_name')->getData();
            $user->setUsername($username);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Votre compte a été créé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("userEdit/{id}", name="userEdit")
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function editUser(User $user, Request $request,
                               UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('password')->getData())) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $user->setUsername($form->get('first_name')->getData() . '.' .
            $form->get('last_name')->getData());
            $this->manager->flush();
            $this->addFlash('success', 'Paramètres modifiés.');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('user/userEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}