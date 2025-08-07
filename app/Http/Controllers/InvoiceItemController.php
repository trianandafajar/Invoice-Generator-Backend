<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Http\Requests\InvoiceItemRequest;
use Illuminate\Http\JsonResponse;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of invoice items for specific invoice
     */
    public function index(Invoice $invoice): JsonResponse
    {
        $items = $invoice->items()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'Invoice items retrieved successfully'
        ]);
    }

    /**
     * Store a newly created invoice item
     */
    public function store(InvoiceItemRequest $request, Invoice $invoice): JsonResponse
    {
        $item = $invoice->items()->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Invoice item created successfully'
        ], 201);
    }

    /**
     * Display the specified invoice item
     */
    public function show(Invoice $invoice, InvoiceItem $item): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Invoice item retrieved successfully'
        ]);
    }

    /**
     * Update the specified invoice item
     */
    public function update(InvoiceItemRequest $request, Invoice $invoice, InvoiceItem $item): JsonResponse
    {
        $item->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Invoice item updated successfully'
        ]);
    }

    /**
     * Remove the specified invoice item
     */
    public function destroy(Invoice $invoice, InvoiceItem $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice item deleted successfully'
        ]);
    }
}
