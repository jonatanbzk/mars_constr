<?php


namespace App\Controller\MaterialController;


use App\Entity\Material;
use App\Form\Material\MaterialType;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as sec;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @sec("is_granted('ROLE_RESP_MATERIEL') or is_granted('ROLE_CONDUCT_TRVX')")
 * @Route("/materials", name="materials_")
 */
class MaterialController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    private function getMaterialListPaginate($entityRepository, $paginator,
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
     *
     * @Route("/index", name="index")
     * @param MaterialRepository $materialRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(MaterialRepository $materialRepository,
                          PaginatorInterface $paginator, Request $request)
    {
        return $this->render(
            'materials/indexMaterials.html.twig', [
            'paginationMaterials' => $this->getMaterialListPaginate(
                $materialRepository, $paginator, $request),
        ]);
    }

    /**
     * @IsGranted("ROLE_RESP_MATERIEL")
     * @Route("/Add", name="Add")
     * @param $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($material);
            $this->manager->flush();
            $this->addFlash('success', 'Matériel ajouté.');
            return $this->redirectToRoute('materials_index');
        }
        return $this->render(
            'materials/addMaterial.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_RESP_MATERIEL")
     * @Route("/edit/{id}", name="edit")
     * @param Material $material
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Material $material, Request $request)
    {
        $formEdit = $this->createForm(MaterialType::class, $material);
        $formEdit->handleRequest($request);
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'Matériel modifié.');
            return $this->redirectToRoute('materials_index');
        }
        return $this->render(
            'materials/editMaterial.html.twig', [
            'formEdit' => $formEdit->createView(),
            'material' => $material
        ]);
    }

    /**
     * @IsGranted("ROLE_RESP_MATERIEL")
     * @Route("/remove/{id}", name="remove", methods="DELETE")
     * @param Request $request
     * @return RedirectResponse
     */
    public function remove(Request $request, Material $material)
    {
        $materialId = (int) $material->getId();
        if (!$material) {
            throw $this->createNotFoundException(
                'No material found for id '.$materialId
            );
        }
        if ($materialId !== 0 && $this->isCsrfTokenValid(
                'delete' . $materialId, $request->get('_token'))) {
            $this->manager->remove($material);
            $this->manager->flush();

            $this->addFlash('success', $material->getName() .
                ' a été supprimé de la base de donnée');
        }
        return $this->redirectToRoute('materials_index');
    }

}