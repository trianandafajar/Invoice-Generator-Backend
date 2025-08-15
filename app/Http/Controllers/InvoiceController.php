<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with('items');

        if ($request->filled('customer_id')) {
            $query->byCustomer($request->customer_id);
        }

        if ($request->boolean('overdue')) {
            $query->unpaid();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $invoices = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $invoices,
            'message' => 'Invoices retrieved successfully'
        ]);
    }

    /**
     * Store a newly created invoice
     */
    public function store(InvoiceRequest $request): JsonResponse
    {
        $data = $request->except(['items', 'logo']);
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('logos', $logoName, 'public');
            $data['logo_path'] = $logoPath;
        }

        $invoice = Invoice::create($data);
        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
        }

        $invoice->load('items');
        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice created successfully'
        ], 201);
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load('items');
        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice retrieved successfully'
        ]);
    }

    /**
     * Update the specified invoice
     */
    public function update(InvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $data = $request->except(['items', 'logo']);
        if ($request->hasFile('logo')) {
            if ($invoice->logo_path && \Storage::disk('public')->exists($invoice->logo_path)) {
                \Storage::disk('public')->delete($invoice->logo_path);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('logos', $logoName, 'public');
            $data['logo_path'] = $logoPath;
        }

        $invoice->update($data);
        $invoice->items()->delete();
        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
        }

        $invoice->load('items');
        return response()->json([
            'success' => true,
            'data' => $invoice,
            'message' => 'Invoice updated successfully'
        ]);
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        if ($invoice->logo_path && \Storage::disk('public')->exists($invoice->logo_path)) {
            \Storage::disk('public')->delete($invoice->logo_path);
        }

        $invoice->items()->delete();
        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Get invoice statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'total_amount' => Invoice::with('items')->get()->sum('total'),
            'overdue_invoices' => Invoice::unpaid()->count(),
            'recent_invoices' => Invoice::with('items')
                ->latest()
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Invoice $invoice): JsonResponse
    {
        $invoice->load('items');

        return response()->json([
            'success' => true,
            'data' => [
                'invoice' => $invoice,
                'pdf_url' => '/invoices/' . $invoice->id . '/pdf'
            ],
            'message' => 'PDF generated successfully'
        ]);
    }

    /**
     * Get logo URL for invoice
     */
    public function getLogo(Invoice $invoice): JsonResponse
    {
        if (!$invoice->logo_path) {
            return response()->json([
                'success' => false,
                'message' => 'Logo not found'
            ], 404);
        }

        $logoUrl = \Storage::disk('public')->url($invoice->logo_path);

        return response()->json([
            'success' => true,
            'data' => [
                'logo_url' => $logoUrl
            ],
            'message' => 'Logo retrieved successfully'
        ]);
    }
}
