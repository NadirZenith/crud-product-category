<?php

namespace App\Manager;

use App\Entity\Category;
use App\Exception\ExceptionCreatingResource;
use App\Exception\ExceptionUpdatingResource;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CategoryManager
{

    protected CategoryRepository $categoryRepository;
    protected ProductRepository $productRepository;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function create(/* array  */$data): Category
    {

        $category = $this->serializer->deserialize($data, Category::class, 'json');

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            throw new ExceptionCreatingResource((string) $errors);
        }

        return $this->categoryRepository->save($category);
    }

    public function get(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function update(int $id, $data): Category
    {
        $category = $this->categoryRepository->findOneBy(['id' => $id]);

        $category = $this->serializer->deserialize($data, Category::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $category
        ]);

        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            throw new ExceptionUpdatingResource((string) $errors);
        }

        return $this->categoryRepository->save($category);
    }

    public function delete(int $id): void
    {
        $products = $this->productRepository->findBy(['category' => $id]);

        foreach ($products as $product) {
            $product->setCategory(null);
            $this->productRepository->save($product);
        }

        $category = $this->categoryRepository->findOneBy(['id' => $id]);

        !$category ?: $this->categoryRepository->remove($category);
    }
}
