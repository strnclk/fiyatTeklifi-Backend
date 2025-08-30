<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index()
    {
        return Quote::query()->with(['customer', 'items'])->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'quote_number' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'tax_rate' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'status' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $quote = Quote::create($data);
        return response()->json($quote->load(['customer', 'items']), 201);
    }

    public function show(Quote $quote)
    {
        return $quote->load(['customer', 'items']);
    }

    public function update(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'customer_id' => 'sometimes|required|integer|exists:customers,id',
            'quote_number' => 'nullable|string|max:50',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'tax_rate' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'status' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $quote->update($data);
        return $quote->load(['customer', 'items']);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->noContent();
    }

    public function pdf(Quote $quote)
    {
        // İlişkilerle birlikte teklifi yükle
        $quote->load(['customer', 'items']);

        // Blade görünümünden PDF üret
        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote,
        ])->setPaper('a4');

        // Tarayıcıda görüntüle (stream) — content-type: application/pdf
        return $pdf->stream('teklif-'.$quote->id.'.pdf');
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'status' => 'required|string|in:draft,sent,accepted,rejected,expired',
        ]);

        $quote->update(['status' => $data['status']]);
        return $quote->fresh()->load(['customer', 'items']);
    }
}
