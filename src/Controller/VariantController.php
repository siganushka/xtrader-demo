<?php

declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Siganushka\ProductBundle\Repository\ProductVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VariantController extends AbstractController
{
    private ProductVariantRepository $productVariantRepository;

    public function __construct(ProductVariantRepository $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Route("/variants")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->productVariantRepository->createQueryBuilder('v');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('variant/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
