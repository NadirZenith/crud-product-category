<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/category")
 */
class CategoryController extends AbstractController
{
    protected CategoryRepository $repository;
    protected SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, CategoryRepository $repository)
    {
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * @Route("", name="create_category", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');

        // @todo validation
        $category = $this->repository->save($category);

        return $this->json($category);
    }

    /**
     * @Route("/{id}", name="update_category", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {

        $category = $this->repository->findOneBy(['id' => $id]);

        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $category
        ]);

        // @todo validation
        $category = $this->repository->save($category);

        return $this->json($category);
    }

    /**
     * @Route("/{id}", name="delete_category", methods={"DELETE"})
     */
    public function delete(int $id, Request $request): Response
    {

        $category = $this->repository->findOneBy(['id' => $id]);

        !$category ?: $this->repository->remove($category);

        return $this->json([]);
    }

    /**
     * @Route("", name="all_category", methods={"GET"})
     */
    public function all(Request $request): Response
    {
        $categories = $this->repository->findAll();

        return $this->json($categories);
    }
}
