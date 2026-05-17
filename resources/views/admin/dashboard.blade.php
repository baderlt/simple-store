@extends('admin.layouts.app')

@section('title', __('dashboard'))
@section('header', __('dashboard'))

@section('content')
<div class="container mx-auto ">

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">{{ __('total_revenue') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalSales, 0) }} DH</h3>
                    <div class="flex items-center mt-2">
                        <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                            <i class="fas fa-arrow-up mr-1"></i> {{ number_format($monthlySales, 0) }} DH {{ __('month') }}
                        </span>
                    </div>
                </div>
                <div class="bg-emerald-100 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">{{ __('Commandes') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOrders }}</h3>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                            {{ __('Aujourd\'hui:') }} {{ $todayOrders }}
                        </span>
                        <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                            {{ __('En attente:') }} {{ $pendingOrders }}
                        </span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">{{ __('Produits') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts }}</h3>
                    <div class="flex items-center mt-2">
                        <span class="text-xs px-2 py-1 {{ $lowStockProducts > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $lowStockProducts }} {{ __('faible stock') }}
                        </span>
                    </div>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Order Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">{{ __('Moy. Commande') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($avgOrderValue, 0) }} DH</h3>
                    <div class="flex items-center mt-2">
                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">
                            {{ $totalCategories }} {{ __('catégories') }}
                        </span>
                    </div>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-chart-bar text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Charts -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">{{ __('monthly_revenue') }} ({{ date('Y') }})</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">Total: {{ number_format($monthlySales, 0) }} DH</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="h-72">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">{{ __('recent_orders') }}</h3>
                        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            {{ __('view_all') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">{{ __('order_number') }}</th>
                                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">{{ __('customer') }}</th>
                                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">{{ __('amount') }}</th>
                                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">{{ __('status') }}</th>
                                    <th class="pb-3 text-left text-sm font-semibold text-gray-600">{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-4">
                                        <p class="font-mono text-sm font-semibold">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="py-4">
                                        <p class="font-medium">{{ Str::limit($order->customer_name, 15) }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                                    </td>
                                    <td class="py-4">
                                        <p class="font-bold text-green-600">{{ number_format($order->total, 2) }} DH</p>
                                    </td>
                                    <td class="py-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
                                                'preparing' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-utensils'],
                                                'out_for_delivery' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-truck'],
                                                'delivered' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
                                                'cancelled' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-times-circle'],
                                            ];
                                            $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $config['color'] }}">
                                            <i class="{{ $config['icon'] }} mr-1"></i>
                                            {{ __('status.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @if($recentOrders->isEmpty())
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-shopping-cart text-3xl mb-3"></i>
                                        <p>{{ __('no_recent_orders') }}</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-8">
            <!-- Low Stock Products -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">{{ __('low_stock') }}</h3>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                            {{ $lowStockProducts }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($lowStockProductsList as $product)
                        <div class="flex items-center justify-between p-3 hover:bg-red-50 rounded-lg transition duration-200">
                            <div class="flex items-center">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-10 h-10 object-cover rounded-lg mr-3">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box-open text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ Str::limit($product->name, 20) }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs text-gray-500">{{ $product->price }} DH</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">
                                    {{ $product->stock_quantity }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ __('stock') }}</p>
                            </div>
                        </div>
                        @endforeach
                        @if($lowStockProductsList->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                            <p class="text-gray-500 text-sm">{{ __('all_stocks_good') }}</p>
                        </div>
                        @else
                        <div class="pt-4 border-t">
                            <a href="{{ route('admin.products.index') }}?stock=low" 
                               class="block w-full text-center bg-red-50 text-red-700 py-2 rounded-lg hover:bg-red-100 transition duration-200 text-sm font-semibold">
                                <i class="fas fa-list mr-2"></i> {{ __('view_low_stock') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Best Selling Products -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">{{ __('admin.dashboard.top_products') }}</h3>
                        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($bestSellingProducts as $index => $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="w-6 h-6 flex items-center justify-center rounded-full mr-3 
                                    @switch($index)
                                        @case(0) bg-yellow-100 text-yellow-800 @break
                                        @case(1) bg-gray-200 text-gray-800 @break
                                        @case(2) bg-orange-100 text-orange-800 @break
                                        @default bg-blue-100 text-blue-800
                                    @endswitch text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">{{ Str::limit($product->name, 25) }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($product->price, 2) }} DH</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">
                                    {{ $product->total_sold ?? 0 }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ __('sold') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-bold text-lg text-gray-800">{{ __('quick_stats') }}</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('status.pending') }}</p>
                                    <p class="font-bold text-gray-800">{{ $pendingOrders }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.index') }}?status=pending" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-utensils text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('status.preparing') }}</p>
                                    <p class="font-bold text-gray-800">{{ $preparingOrders }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.index') }}?status=preparing" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-truck text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('status.out_for_delivery') }}</p>
                                    <p class="font-bold text-gray-800">{{ $deliveryOrders }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.index') }}?status=out_for_delivery" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-lg text-white mb-4">{{ __('quick_actions') }}</h3>
                <div class="space-y-3">
                    
                    <a href="{{ route('admin.products.create') }}" 
                       class="flex items-center justify-center w-full bg-emerald-500 text-white py-3 px-4 rounded-lg hover:bg-emerald-600 transition duration-200">
                        <i class="fas fa-box mr-3"></i>
                        <span class="font-semibold">{{ __('add_product') }}</span>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-center w-full bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-list mr-3"></i>
                        <span class="font-semibold">{{ __('all_orders') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode([__('jan'), __('feb'), __('mar'), __('apr'), __('may'), __('jun'), __('jul'), __('aug'), __('sep'), __('oct'), __('nov'), __('dec')]) !!},
            datasets: [{
                label: "{{ __('revenue_dh') }}",
                data: {!! json_encode(array_values($revenueData)) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' DH';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection