<?php


namespace App\Controller\EmployeeController;

use App\Entity\User;
use App\Entity\Worker;
use App\Form\User\UserManagerSiteType;
use App\Form\Worker\WorkerType;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as sec;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


/**
 * @sec("is_granted('ROLE_RH') or is_granted('ROLE_CONDUCT_TRVX')")
 * @Route("/employee", name="employee_")
 */
class EmployeeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $manager,
                                Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    private function getEntityListPaginate($entityRepository, $paginator,
                                         $request)
    {
        $currentPage = 1;
        if (isset($_GET['page'])) {
            $currentPage = (int)$_GET['page'];
        }
        return $paginator->paginate(
            $entityRepository->findAllQuery(),
            $request->query->getInt('page', $currentPage),
            10
        );
    }

    /**
     * @Route("/indexWorkers", name="indexWorkers")
     * @param WorkerRepository $workerRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexWorkers(WorkerRepository $workerRepository,
                             PaginatorInterface $paginator, Request $request)
    {
        return $this->render('employee/workers/workersList.html.twig', [
            'paginationWorkers' => $this->getEntityListPaginate
            ($workerRepository, $paginator, $request),
        ]);
    }

    /**
     * @Route("/indexManagers", name="indexManagers")
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexManagers(UserRepository $userRepository,
                          PaginatorInterface $paginator, Request $request)
    {
        return $this->render('employee/managers/managersList.html.twig', [
            'paginationUsers' => $this->getEntityListPaginate
            ($userRepository, $paginator, $request),
        ]);
    }

    /**
     * @Route("/indexConstrSiteManagers", name="indexConstrSiteManagers")
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexConstrSiteManagers(UserRepository $userRepository,
                                  PaginatorInterface $paginator, Request $request)
    {
        return $this->render('employee/constructSiteManagers/constructSiteManagersList.html.twig', [
            'paginationConstrManag' => $this->getEntityListPaginate
            ($userRepository, $paginator, $request),
        ]);
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/constructManagSiteEdit/{id}", name="constructManagSiteEdit")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editManagerSite(User $user, Request $request)
    {
        $form = $this->createForm(UserManagerSiteType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Chantier modifié.');
            return $this->redirectToRoute('employee_indexConstrSiteManagers');
        }
        return $this->render('employee/constructSiteManagers/editManagerSite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_RH")
     * @Route("/workerAdd", name="workerAdd")
     * @param $request
     * @return RedirectResponse|Response
     */
    public function addWorker(Request $request)
    {
        $worker = new Worker();
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($worker);
            $this->manager->flush();
            $this->addFlash('success', 'Compagnon ajouté.');
            return $this->redirectToRoute('employee_indexWorkers');
        }
        return $this->render('employee/workers/workerAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/workerEdit/{id}", name="workerEdit")
     * @param Worker $worker
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editWorker(Worker $worker, Request $request)
    {
        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Compagnon modifié.');
            return $this->redirectToRoute('employee_indexWorkers');
        }
        return $this->render('employee/workers/workerEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/workerRemove/{id}", name="workerRemove", methods="DELETE")
     * @param Request $request
     * @param Worker $worker
     * @return RedirectResponse
     */
    public function removeWorker(Request $request, Worker $worker)
    {
        $workerId = (int) $worker->getId();
        if (!$worker) {
            throw $this->createNotFoundException(
                'No worker found for id '.$workerId
            );
        }
        if ($workerId !== 0 && $this->isCsrfTokenValid(
                'delete' . $workerId, $request->get('_token'))) {
            $this->manager->remove($worker);
            $this->manager->flush();

            $this->addFlash('success', $worker->getFirstName() .
                ' ' . $worker->getLastName() . ' a été supprimé de la base de 
                donnée');
        }
        return $this->redirectToRoute('employee_indexWorkers');
    }

    /**
     * @IsGranted("ROLE_RH")
     * @Route("/manager/edit/{id}", name="managerUpdate")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return RedirectResponse
     */
    public function editUser(UserRepository $userRepository, Request $request)
    {
        $userId = (int) $routeParameters = $request->attributes->get('id');
        $user = $userRepository->find($userId);
        if ($userId != 0 && $this->isCsrfTokenValid(
                'updateManager' . $userId, $request->get('_token'))) {
            $roles = ["ROLE_USER"];
            if (!empty($request->get('role_rh'))) {
                $roles[] = $request->get('role_rh');
            }
            if (!empty($request->get('role_conduct_trvx'))) {
                $roles[] = $request->get('role_conduct_trvx');
            }
            if (!empty($request->get('role_resp_materiel'))) {
                $roles[] = $request->get('role_resp_materiel');
            }
            if (!empty($request->get('fonction'))) {
                $user->setGrade($request->get('fonction'));
            }
            $currentRole = $user->getRoles();
            if ($currentRole !== $roles) {
                $user->setRoles($roles);
            }

            $this->manager->flush();

            if ($currentRole !== $roles && $user === $this->security->getUser()) {
                return $this->redirectToRoute('app_logout');
            }
            return $this->redirectToRoute('employee_indexManagers');

        }
    }

    /**
     * @Route("/managerRemove/{id}", name="managerRemove", methods="DELETE")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeManager(UserRepository $userRepository, Request
    $request)
    {
        $userId = (int) $routeParameters = $request->attributes->get('id');
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException(
                'No manager found for id '.$userId
            );
        }
        if ($userId !== 0 && $this->isCsrfTokenValid(
                'delete' . $userId, $request->get('_token'))) {
            $this->manager->remove($user);
            $this->manager->flush();

            $this->addFlash('success', $user->getFirstName() .
                ' ' . $user->getLastName() . ' a été supprimé de la base de 
                donnée');
        }
        return $this->redirectToRoute('employee_indexManagers');
    }

}