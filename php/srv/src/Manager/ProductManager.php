<?php

namespace App\Manager;

use App\Exception\ExceptionCreatingResource;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ExchangeService;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Product;

class ProductManager
{

    protected ProductRepository $productRepository;
    protected CategoryRepository $categoryRepository;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected ExchangeService $exchangeService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ExchangeService $exchangeService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->exchangeService = $exchangeService;
    }

    public function create(/* array  */$data): Product
    {
        $content = json_decode($data, true);
        $categoryId = $content['category'] ?? null;

        $product = $this->serializer->deserialize($data, Product::class, 'json');
        if ($categoryId && $category = $this->categoryRepository->find($categoryId)) {
            $product->setCategory($category);
        }

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            throw new ExceptionCreatingResource((string) $errors);
        }

        return $this->productRepository->save($product);
    }

    public function get(): array
    {
        return $this->productRepository->findAll();
    }

    public function getFeatured(string $currency): array
    {
        $products = $this->productRepository->findBy(['featured' => true]);

        return $this->toCurrency($currency, $products);
    }

    protected function toCurrency($currency, array $products): array
    {

        foreach ($products as $product) {
            $price = $product->getPrice();
            $productCurrency = $product->getCurrency();

            if ('EUR' === $productCurrency && 'USD' === $currency) {
                $price = $this->exchangeService->getPriceUSD($price);
            } elseif ('USD' === $productCurrency && 'EUR' === $currency) {
                $price = $this->exchangeService->getPriceEUR($price);
            }

            $product->setPrice($price);
            $product->setCurrency($currency);
        }

        return $products;
    }
}
