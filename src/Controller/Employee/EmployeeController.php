<?php


namespace App\Controller\Employee;


use App\Repository\WorkerRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    /**
     * @Route("/employee", name="employee")
     * @param WorkerRepository $workerRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function homepage(WorkerRepository $workerRepository,
                             PaginatorInterface $paginator, Request $request)
    {
        $currentPage = 1;
        if (isset($_GET['page'])) {
            $currentPage = (int)$_GET['page'];
        }
        $pagination = $paginator->paginate(
            $workerRepository->findAllQuery(),
            $request->query->getInt('page', $currentPage),
            10
        );
        return $this->render('employee/employee.html.twig', [
            'pagination' => $pagination,
        ]);

    }
}