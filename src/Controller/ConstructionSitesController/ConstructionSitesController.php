<?php


namespace App\Controller\ConstructionSitesController;


use App\Entity\ConstructionSite;
use App\Entity\Worker;
use App\Form\Construction\ConstructionSiteType;
use App\Form\Worker\WorkerType;
use App\Repository\ConstructionSiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/constructionSites", name="constructionSites_")
 */
class ConstructionSitesController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    private function getConstructionListPaginate($entityRepository, $paginator,
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
     * @Route("/index", name="index")
     * @param ConstructionSiteRepository $constructionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(ConstructionSiteRepository $constructionRepository,
                          PaginatorInterface $paginator, Request $request)
    {
        return $this->render(
            'constructionSites/indexConstructionSites.html.twig', [
            'paginationConstructionSites' => $this->getConstructionListPaginate(
                $constructionRepository, $paginator, $request),
        ]);
    }

    /**
     * @Route("/display/{id}", name="display")
     * @param ConstructionSite $site
     * @param Request $request
     * @return Response
     */
    public function display(ConstructionSite $site, Request $request)
    {
        return $this->render(
            'constructionSites/display.html.twig', [
                'site' => $site
        ]);
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/Add", name="Add")
     * @param $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $construction = new ConstructionSite();
        $form = $this->createForm(ConstructionSiteType::class, $construction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($construction);
            $this->manager->flush();
            $this->addFlash('success', 'Chantier ajouté.');
            return $this->redirectToRoute('constructionSites_index');
        }
        return $this->render(
            'constructionSites/addConstructionSite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/edit/{id}", name="edit")
     * @param ConstructionSite $site
     * @param Request $request
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     */
    public function editSite(ConstructionSite $site, Request $request,
                                UserRepository $userRepository)
    {
        $formEdit = $this->createForm(ConstructionSiteType::class, $site);
        $formEdit->handleRequest($request);
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Chantier modifié.');
            return $this->redirectToRoute('constructionSites_index');
        }
        $managersAvailable = [];
        $siteManagers = [];
        $managersList = $userRepository->findConstructionManagers();
        foreach ($managersList as $manager) {
            if ($manager->getConstructionSite() === null) {
                $managersAvailable[] = $manager;
            }
            else if ($manager->getConstructionSite()->getId()
                === $site->getId()) {
                $siteManagers[] = $manager;
            }
        }
        return $this->render(
            'constructionSites/editConstruction.html.twig', [
            'formEdit' => $formEdit->createView(),
            'managersAvailable' => $managersAvailable,
            'siteManager' => $siteManagers,
                'site' => $site
        ]);
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/editManager/{id}", name="editManager")
     * @param ConstructionSite $site
     * @param Request $request
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function editSiteManager(ConstructionSite $site, Request $request,
                             UserRepository $userRepository)
    {
        $userId = $request->get('managerId');
        $user = $userRepository->find($userId);

        if (!empty($user) && $this->isCsrfTokenValid('update' . $site->getId(),
            $request->get('_token'))) {
            $site->addUser($user);
            $this->manager->flush();
        }
        return $this->redirectToRoute('constructionSites_index');
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/removeManager/{id}", name="removeManager", methods="DELETE")
     * @param ConstructionSite $site
     * @param Request $request
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function removeSiteManager(ConstructionSite $site, Request $request,
                                    UserRepository $userRepository)
    {
        $userId = $request->get('managerId');
        $user = $userRepository->find($userId);

        if (!empty($user) && $this->isCsrfTokenValid('remove' . $site->getId(),
                $request->get('_token'))) {
            $site->removeUser($user);
            $this->manager->flush();
        }
        return $this->redirectToRoute('constructionSites_index');
    }

    /**
     * @IsGranted("ROLE_CONDUCT_TRVX")
     * @Route("/remove/{id}", name="remove", methods="DELETE")
     * @param Request $request
     * @param ConstructionSite $site
     * @return RedirectResponse
     */
    public function remove(Request $request, ConstructionSite $site)
    {
        $siteId = (int) $site->getId();
        if (!$site) {
            throw $this->createNotFoundException(
                'No worker found for id '.$siteId
            );
        }
        if ($siteId !== 0 && $this->isCsrfTokenValid(
                'delete' . $siteId, $request->get('_token'))) {
            $this->manager->remove($site);
            $this->manager->flush();

            $this->addFlash('success', $site->getName() .
                ' a été supprimé de la base de donnée');
        }
        return $this->redirectToRoute('constructionSites_index');
    }
}