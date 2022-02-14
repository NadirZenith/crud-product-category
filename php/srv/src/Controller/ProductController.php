<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/product", name="api_product_")
 */
class ProductController extends AbstractController
{
    protected ProductRepository $repository;
    protected CategoryRepository $categoryRepository;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ProductRepository $repository, CategoryRepository $categoryRepository, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $categoryId = $content['category'] ?? null;

        $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json');

        if ($categoryId && $category = $this->categoryRepository->find($categoryId)) {
            $product->setCategory($category);
        }

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {

            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // @todo validation
        $product = $this->repository->save($product);

        return $this->json($product);
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(Request $request): Response
    {
        $products = $this->repository->findAll();

        return $this->json($products);
    }
}
