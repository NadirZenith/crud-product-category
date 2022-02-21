<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/category", name="api_category_")
 */
class CategoryController extends AbstractController
{
    protected CategoryRepository $repository;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected ProductRepository $productRepository;

    public function __construct(SerializerInterface $serializer, CategoryRepository $repository, ValidatorInterface $validator, ProductRepository $productRepository)
    {
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {

            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $category = $this->repository->save($category);

        return $this->json($category, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {

        $category = $this->repository->findOneBy(['id' => $id]);

        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $category
        ]);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {

            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // @todo validation
        $category = $this->repository->save($category);

        return $this->json($category);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {

        $products = $this->productRepository->findBy(['category' => $id]);

        foreach ($products as $product) {
            $product->setCategory(null);
            $this->productRepository->save($product);
        }

        $category = $this->repository->findOneBy(['id' => $id]);

        !$category ?: $this->repository->remove($category);

        return $this->json([]);
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(Request $request): Response
    {
        $categories = $this->repository->findAll();

        return $this->json($categories);
    }
}
