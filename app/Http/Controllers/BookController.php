<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Interfaces\BookRepositoryInterface;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookController extends Controller
{
    use ApiResponseHelpers;

    private BookRepositoryInterface $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository) {
        $this->bookRepository = $bookRepository;
    }

    public function index(Request $request): JsonResponse {
        $books = $this->bookRepository->search($request->all());
        if(empty($books->count()))
            return $this->respondNotFound('There are no books');

        return $this->respondWithSuccess($books);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookRepository->create($request->validated());
        return $this->respondCreated($book);
    }

    public function show(int $id): JsonResponse {
        try {
            $book = $this->bookRepository->get($id);
        } catch (\Throwable $e) {
            return $this->respondNotFound('The book was not found');
        }
        return $this->respondWithSuccess($book);
    }

    public function update(UpdateBookRequest $request, int $id): JsonResponse {
        try {
            $book = $this->bookRepository->update($request->validated(), $id);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage());
        }
        return $this->respondWithSuccess($book);
    }

    public function destroy(int $id): JsonResponse {
        try {
            $this->bookRepository->delete($id);
        } catch (\Throwable $e) {
            return $this->respondError($e->getMessage());
        }
        return $this->respondOk('The book has been deleted');
    }
}
