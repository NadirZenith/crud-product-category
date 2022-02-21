<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\ExceptionCreatingResource;
use App\Exception\ExceptionUpdatingResource;
use App\Manager\CategoryManager;

/**
 * @Route("/api/categories", name="api_categories_")
 */
class CategoryController extends AbstractController
{
    protected CategoryManager $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        try {
            return $this->json(
                $this->categoryManager->create($request->getContent()),
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
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {

        try {
            return $this->json(
                $this->categoryManager->update($id, $request->getContent()),
                Response::HTTP_CREATED
            );
        } catch (ExceptionUpdatingResource $exception) {
            return $this->json(
                [
                    'errors' => $exception->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $this->categoryManager->delete($id);
        return $this->json([]);
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(): Response
    {
        return $this->json(
            $this->categoryManager->get()
        );
    }
}
