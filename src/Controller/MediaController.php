<?php

declare(strict_types=1);

namespace App\Controller;

use App\Media\TestPdf;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\MediaBundle\Form\MediaUploadType;
use Siganushka\MediaBundle\Form\Type\MediaChannelType;
use Siganushka\MediaBundle\Form\Type\MediaFileType;
use Siganushka\MediaBundle\Form\Type\MediaType;
use Siganushka\MediaBundle\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaController extends AbstractController
{
    private MediaRepository $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @Route("/media")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->mediaRepository->createQueryBuilder('m');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('media/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/media/{hash}/delete")
     */
    public function delete(EntityManagerInterface $entityManager, string $hash): Response
    {
        $entity = $this->mediaRepository->findOneByHash($hash);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $hash));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Resource #%s has been deleted!', $hash));

        return $this->redirectToRoute('app_media_index');
    }

    /**
     * @Route("/media/MediaUploadType")
     */
    public function MediaUploadType(Request $request): Response
    {
        $form = $this->createForm(MediaUploadType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/media/MediaType")
     */
    public function MediaType(Request $request): Response
    {
        $media = $this->mediaRepository->findAll();
        $data = [
            'media1' => $media[0] ?? null,
        ];

        $builder = $this->createFormBuilder($data)
            ->add('media0', MediaType::class, [
                'label' => '默认状态',
            ])
            ->add('media1', MediaType::class, [
                'label' => '有值状态',
            ])
            ->add('media2', MediaType::class, [
                'label' => '错误状态',
            ])
            ->add('media3', MediaType::class, [
                'label' => '禁用状态',
                'disabled' => true,
            ])
            ->add('media4', MediaType::class, [
                'label' => '自定义尺寸',
                'style' => 'width: 100%; height: 240px',
            ])
            ->add('media5', MediaType::class, [
                'label' => '自定义文件类型',
                'channel' => TestPdf::class,
            ])
            ->add('media6', MediaType::class, [
                'label' => '自定义表单验证',
                'constraints' => new NotBlank(),
            ])
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        $form['media2']->addError(new FormError('This value should not be blank.'));

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/media/MediaChannelType")
     */
    public function MediaChannelType(Request $request): Response
    {
        $builder = $this->createFormBuilder()
            ->add('channel', MediaChannelType::class, [
                'label' => 'media.channel',
            ])
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/media/MediaFileType")
     */
    public function MediaFileType(Request $request): Response
    {
        $builder = $this->createFormBuilder()
            ->add('file', MediaFileType::class, [
                'label' => 'media.file',
                'constraints' => new NotBlank(),
            ])
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
