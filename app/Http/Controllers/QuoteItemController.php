<?php

namespace App\Http\Controllers;

use App\Models\QuoteItem;
use Illuminate\Http\Request;

class QuoteItemController extends Controller
{
    public function index()
    {
        return QuoteItem::query()->with(['quote', 'service'])->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quote_id' => 'required|integer|exists:quotes,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'service_name' => 'nullable|string|max:255',
            'quantity' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'subtotal' => 'nullable|numeric',
        ]);

        $item = QuoteItem::create($data);
        return response()->json($item->load(['quote', 'service']), 201);
    }

    public function show(QuoteItem $quote_item)
    {
        return $quote_item->load(['quote', 'service']);
    }

    public function update(Request $request, QuoteItem $quote_item)
    {
        $data = $request->validate([
            'quote_id' => 'sometimes|required|integer|exists:quotes,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'service_name' => 'nullable|string|max:255',
            'quantity' => 'sometimes|required|numeric',
            'unit_price' => 'sometimes|required|numeric',
            'subtotal' => 'nullable|numeric',
        ]);

        $quote_item->update($data);
        return $quote_item->load(['quote', 'service']);
    }

    public function destroy(QuoteItem $quote_item)
    {
        $quote_item->delete();
        return response()->noContent();
    }
}
