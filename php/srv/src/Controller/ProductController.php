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
use App\Manager\ProductManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/products", name="api_products_")
 */
class ProductController extends AbstractController
{
    protected ProductRepository $repository;
    protected CategoryRepository $categoryRepository;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected ProductManager $productManager;

    public function __construct(SerializerInterface $serializer, ProductRepository $repository, CategoryRepository $categoryRepository, ValidatorInterface $validator, ProductManager $productManager)
    {
        $this->serializer = $serializer;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
        $this->productManager = $productManager;
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

        return $this->json($product, Response::HTTP_CREATED);
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(Request $request): Response
    {
        $products = $this->repository->findAll();

        return $this->json($products);
    }

    /**
     * @Route("/featured", name="featured", methods={"GET"})
     */
    public function featured(Request $request): Response
    {
        $currency = $request->query->get('currency', 'EUR');

        if (!in_array($currency, ['EUR', 'USD'])) {
            $currency = 'EUR';
        }

        $products = $this->repository->findBy(['featured' => true]);


        $products = $this->productManager->toCurrency($currency, $products);

        return $this->json($products);
    }
}
