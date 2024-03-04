<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\ProductBundle\Entity\Option;
use Siganushka\ProductBundle\Form\ProductType;
use Siganushka\ProductBundle\Form\ProductVariantCollectionType;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/products")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('p');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/products/new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->productRepository->createNew();
        // $entity->addOption($entityManager->find(Option::class, 1));

        $form = $this->createForm(ProductType::class, $entity);
        $form->add('save', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/{id<\d+>}/edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/{id<\d+>}/delete")
     */
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->redirectToRoute('app_product_index');
    }

    /**
     * @Route("/products/ProductType")
     */
    public function ProductType(Request $request): Response
    {
        $form = $this->createForm(ProductType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/ProductVariantCollectionType")
     */
    public function ProductVariantCollectionType(Request $request): Response
    {
        $form = $this->createForm(ProductVariantCollectionType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/ProductVariantType")
     */
    public function ProductVariantType(Request $request): Response
    {
        $form = $this->createForm(ProductVariantType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
