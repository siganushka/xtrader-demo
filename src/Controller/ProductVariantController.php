<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\ProductBundle\Form\ProductVariantCollectionType;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Repository\ProductRepository;
use Siganushka\ProductBundle\Repository\ProductVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductVariantController extends AbstractController
{
    private ProductRepository $productRepository;
    private ProductVariantRepository $productVariantRepository;

    public function __construct(ProductRepository $productRepository, ProductVariantRepository $productVariantRepository)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Route("/products/{productId<\d+>}/variants")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, int $productId): Response
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $productId));
        }

        $form = $this->createForm(ProductVariantCollectionType::class, $product);
        $form->add('submit', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);
        // dd($form->createView());

        if ($form->isSubmitted() && $form->isValid()) {
            // dd(__METHOD__, $product->getVariants()->toArray());

            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_productvariant_index', compact('productId'));
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/{productId<\d+>}/variants/new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, int $productId): Response
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $productId));
        }

        $variant = $this->productVariantRepository->createNew();
        $variant->setProduct($product);

        $form = $this->createForm(ProductVariantType::class, $variant);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($variant);
            $entityManager->flush();

            return $this->redirectToRoute('app_productvariant_index', compact('productId'));
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/variants/{id<\d+>}/edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productVariantRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductVariantType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_variant_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/variants/{id<\d+>}/delete")
     */
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productVariantRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Resource #%d has been deleted!', $id));

        return $this->redirectToRoute('app_variant_index', [
            'productId' => $entity->getProduct()->getId(),
        ]);
    }
}
