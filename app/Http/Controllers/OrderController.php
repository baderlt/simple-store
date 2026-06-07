<?php
// app/Http/Controllers/OrderController.php (User Orders)
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'items.variant'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}