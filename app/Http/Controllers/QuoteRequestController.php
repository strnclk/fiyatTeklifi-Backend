<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            // Customer info (public form)
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:20',
            // Quote info (optional)
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // If email provided, try to find existing customer; else create new
        $customer = null;
        if (!empty($data['email'])) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                [
                    'company_name' => $data['company_name'],
                    'contact_person' => $data['contact_person'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'tax_number' => $data['tax_number'] ?? null,
                ]
            );
        } else {
            $customer = Customer::create([
                'company_name' => $data['company_name'],
                'contact_person' => $data['contact_person'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
            ]);
        }

        $quote = Quote::create([
            'customer_id' => $customer->id,
            'quote_number' => null, // generate later
            'title' => $data['title'] ?? 'Teklif Talebi',
            'description' => $data['description'] ?? null,
            'subtotal' => null,
            'tax_rate' => null,
            'tax_amount' => null,
            'total_amount' => null,
            'status' => 'draft',
            'valid_until' => $data['valid_until'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json($quote->load(['customer', 'items']), 201);
    }
}
