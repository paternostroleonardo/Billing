<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counter;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB as FacadesDB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = Invoice::orderBy('created_at', 'desc')->paginate(15);

        return response()
            ->json(['invoices' => $results]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $counter = Counter::where('key', 'invoice')->first();

        $form = [
            'number' => $counter->prefix . $counter->value,
            'issuer_name' => null,
            'issuer_nit' => null,
            'receiver_name' => null,
            'receiver_nit' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'out_iva' => null,
            'iva' => null,
            'discount' => 0,
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'qty' => 1
                ]
            ]
        ];

        return response()
            ->json(['form' => $form]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'issuer_name' => 'required|string',
            'issuer_nit' => 'required|string',
            'receiver_name' => 'required|string',
            'receiver_nit' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'out_iva' => 'required|numeric',
            'iva' => 'required|numeric',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        $invoice = new Invoice;
        $invoice->fill($request->except('items'));

        $invoice->sub_total = collect($request->items)->sum(function ($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $invoice = FacadesDB::transaction(function () use ($invoice, $request) {
            $counter = Counter::where('key', 'invoice')->first();
            $invoice->number = $counter->prefix . $counter->value;

            $invoice->storeHasMany([
                'items' => $request->items
            ]);

            $counter->increment('value');

            return $invoice;
        });

        return response()
            ->json(['saved' => true, 'id' => $invoice->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Invoice::with(['items.product'])
            ->findOrFail($id);

        return response()
            ->json(['model' => $model]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $form = Invoice::with(['items.product'])
            ->findOrFail($id);

        return response()
            ->json(['form' => $form]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'issuer_name' => 'required|string',
            'issuer_nit' => 'required|string',
            'receiver_name' => 'required|string',
            'receiver_nit' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'out_iva' => 'required|numeric',
            'iva' => 'required|numeric',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        $invoice->fill($request->except('items'));

        $invoice->sub_total = collect($request->items)->sum(function ($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $invoice = FacadesDB::transaction(function () use ($invoice, $request) {
            $invoice->updateHasMany([
                'items' => $request->items
            ]);

            return $invoice;
        });

        return response()
            ->json(['guardada' => true, 'id' => $invoice->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->items()->delete();

        $invoice->delete();

        return response()
            ->json(['borrada' => true]);
    }
}
