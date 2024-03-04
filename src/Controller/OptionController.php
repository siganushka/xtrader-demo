<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\ProductBundle\Form\OptionType;
use Siganushka\ProductBundle\Form\OptionValueType;
use Siganushka\ProductBundle\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OptionController extends AbstractController
{
    private OptionRepository $optionRepository;

    public function __construct(OptionRepository $optionRepository)
    {
        $this->optionRepository = $optionRepository;
    }

    /**
     * @Route("/options")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->optionRepository->createQueryBuilder('o');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('option/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/options/new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->optionRepository->createNew();

        $form = $this->createForm(OptionType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('app_option_index');
        }

        return $this->render('option/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/options/{id<\d+>}/edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->optionRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(OptionType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_option_index');
        }

        return $this->render('option/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/options/{id<\d+>}/delete")
     */
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->optionRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->redirectToRoute('app_option_index');
    }

    /**
     * @Route("/options/OptionType")
     */
    public function OptionType(Request $request): Response
    {
        $form = $this->createForm(OptionType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('option/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/options/OptionValueType")
     */
    public function OptionValueType(Request $request): Response
    {
        $form = $this->createForm(OptionValueType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('option/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
