<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\ExceptionCreatingResource;
use App\Manager\ProductManager;
use App\Model\Currency;

/**
 * @Route("/api/products", name="api_products_")
 */
class ProductController extends AbstractController
{
    protected ProductManager $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        try {
            return $this->json(
                $this->productManager->create($request->getContent()),
                Response::HTTP_CREATED
            );
        } catch (ExceptionCreatingResource $exception) {
            return $this->json(
                [
                    'errors' => $exception->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(): Response
    {
        return $this->json($this->productManager->get());
    }

    /**
     * @Route("/featured", name="featured", methods={"GET"})
     */
    public function featured(Request $request): Response
    {
        return $this->json(
            $this->productManager->getFeatured(
                $request->query->get('currency', Currency::EUR)
            )
        );
    }
}
