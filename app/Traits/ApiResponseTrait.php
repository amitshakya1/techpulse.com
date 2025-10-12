<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Return a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Return a paginated response.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function paginatedResponse($data, string $message = 'Success')
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }
}
