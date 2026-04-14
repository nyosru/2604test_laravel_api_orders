<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderIndexRequest;
use App\Http\Requests\Api\V1\OrderStoreRequest;
use App\Http\Requests\Api\V1\OrderUpdateStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    #[OA\Get(
        path: '/api/v1/orders',
        operationId: 'listOrders',
        summary: 'Get orders list',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Filter by order status', schema: new OA\Schema(type: 'string', enum: ['new', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'])),
            new OA\Parameter(name: 'customer_id', in: 'query', required: false, description: 'Filter by customer identifier', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'date_from', in: 'query', required: false, description: 'Created from date', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'date_to', in: 'query', required: false, description: 'Created to date', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Pagination size', schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated orders list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Order')
                        ),
                        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function index(OrderIndexRequest $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->paginate($request->toDto());

        return OrderResource::collection($orders);
    }

    #[OA\Post(
        path: '/api/v1/orders',
        operationId: 'createOrder',
        summary: 'Create order',
        tags: ['Orders'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Order payload with customer and items',
            content: new OA\JsonContent(ref: '#/components/schemas/CreateOrderRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error or insufficient stock',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function store(OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->toDto());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/v1/orders/{order}',
        operationId: 'showOrder',
        summary: 'Get order details',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'Order identifier', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order details',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function show(int $order): OrderResource
    {
        return new OrderResource($this->orderService->getById($order));
    }

    #[OA\Patch(
        path: '/api/v1/orders/{order}/status',
        operationId: 'updateOrderStatus',
        summary: 'Update order status',
        tags: ['Orders'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, description: 'Order identifier', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'New order status',
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderStatusRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order status updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function updateStatus(OrderUpdateStatusRequest $request, int $order): OrderResource
    {
        return new OrderResource($this->orderService->updateStatus($request->toDto($order)));
    }
}
