<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Siganushka\ProductBundle\Repository\ProductOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductOptionController extends AbstractController
{
    private ProductOptionRepository $productOptionRepository;

    public function __construct(ProductOptionRepository $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * @Route("/product-options/{id<\d+>}/edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productOptionRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductOptionType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product-option/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
