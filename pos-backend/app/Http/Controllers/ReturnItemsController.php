<?php

namespace App\Http\Controllers;

use App\Models\ReturnItem;
use App\Models\SalesReturnItem;
use Illuminate\Http\Request;

class ReturnItemsController extends Controller
{
    public function index()
    {
        // Fetch all return items with related sales return data
        $returnItems = ReturnItem::with(['salesReturnItem'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reason' => $item->reason,
                    'quantity' => $item->quantity,
                    'product_id' => $item->product_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'sales_id' => $item->salesReturnItem ? $item->salesReturnItem->sales_id : null,
                    'returned_at' => $item->salesReturnItem ? $item->salesReturnItem->returned_at : null
                ];
            });
        return response()->json([
            'status' => 'success',
            'data' => $returnItems
        ]);
    }

    public function show($id)
    {
        // Fetch a specific return item by ID
        $returnItem = ReturnItem::find($id);
        if (!$returnItem) {
            return response()->json([
                'error' => 'Return item not found',
                'id' => $id
            ], 404);
        }
        return response()->json($returnItem);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'reason' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:1',
            'product_id' => 'sometimes|required|exists:products,id'
        ]);

        $returnItem = ReturnItem::find($id);
        if (!$returnItem) {
            return response()->json([
                'error' => 'Return item not found',
                'id' => $id
            ], 404);
        }

        $returnItem->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => $returnItem
        ]);
    }

    public function destroy($id)
    {
        $returnItem = ReturnItem::find($id);
        if (!$returnItem) {
            return response()->json([
                'error' => 'Return item not found',
                'id' => $id
            ], 404);
        }

        $returnItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Return item deleted successfully'
        ]);
    }
}
