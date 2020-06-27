<?php


namespace App\Controller\Employee;


use App\Entity\User;
use App\Entity\Worker;
use App\Form\Worker\WorkerType;
use App\Repository\UserRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


/**
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

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    private function getWorkers($workerRepository, $paginator, $request)
    {
        $currentPage = 1;
        if (isset($_GET['page'])) {
            $currentPage = (int)$_GET['page'];
        }
        return $paginator->paginate(
            $workerRepository->findAllQuery(),
            $request->query->getInt('page', $currentPage),
            10
        );
    }

    private function getManagers($userRepository, $paginator, $request)
    {
        $currentPage = 1;
        if (isset($_GET['page'])) {
            $currentPage = (int)$_GET['page'];
        }
        return $paginator->paginate(
            $userRepository->findAllQuery(),
            $request->query->getInt('page', $currentPage),
            10
        );
    }

    /**
     * @Route("/index", name="index")
     * @param WorkerRepository $workerRepository
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(WorkerRepository $workerRepository,
                          UserRepository $userRepository,
                             PaginatorInterface $paginator, Request $request)
    {
        return $this->render('employee/employee.html.twig', [
            'paginationWorkers' => $this->getWorkers($workerRepository,
                $paginator, $request),
            'paginationUsers' => $this->getManagers($userRepository, $paginator,
                $request),
        ]);
    }

    /**
     * @IsGranted("ROLE_RH")
     * @Route("workerAdd", name="workerAdd")
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
            return $this->redirectToRoute('employee_index');
        }
        return $this->render('employee/workerAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_RH")
     * @Route("workerEdit/{id}", name="workerEdit")
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
            return $this->redirectToRoute('employee_index');
        }
        return $this->render('employee/workerEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("workerRemove/{id}", name="workerRemove", methods="DELETE")
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
        return $this->redirectToRoute('employee_index');
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

            if ($user->getRoles() !== $roles) {
                $user->setRoles($roles);
            }

            $this->manager->flush();

            if ($user === $this->security->getUser() &&
                $user->getRoles() !== $roles) {
                return $this->redirectToRoute('app_logout');
            }
            return $this->redirectToRoute('employee_index');
        }
    }

    /**
     * @Route("managerRemove/{id}", name="managerRemove", methods="DELETE")
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
        return $this->redirectToRoute('employee_index');
    }

}