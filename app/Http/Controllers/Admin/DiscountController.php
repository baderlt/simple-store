<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with(['product', 'category'])
            ->latest()
            ->paginate(15);
        
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // Ensure at least one discount type is provided
        if (empty($validated['discount_percentage']) && empty($validated['fixed_amount'])) {
            return redirect()->back()
                ->withErrors(['discount_percentage' => 'Veuillez entrer soit un pourcentage, soit un montant fixe.'])
                ->withInput();
        }

        // Convert fixed amount to percentage if provided
        if (!empty($validated['fixed_amount'])) {
            $product = Product::find($validated['product_id']);
            
            if (!$product) {
                return redirect()->back()
                    ->withErrors(['product_id' => 'Produit non trouvé.'])
                    ->withInput();
            }
            
            if ($product->price <= 0) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le prix du produit doit être supérieur à 0 pour calculer un pourcentage.'])
                    ->withInput();
            }
            
            // Calculate percentage from fixed amount
            $percentage = ($validated['fixed_amount'] / $product->price) * 100;
            
            // Ensure percentage doesn't exceed 100%
            if ($percentage > 100) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le montant fixe ne peut pas dépasser le prix du produit (100%).'])
                    ->withInput();
            }
            
            $validated['discount_percentage'] = $percentage;
        }
        
        // Remove fixed_amount from validated data since we only store percentage
        unset($validated['fixed_amount']);

        $validated['is_active'] = $request->has('is_active');
        $validated['type'] = 'product';
        $validated['category_id'] = null;

        // Check for overlapping discounts on the same product
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $productId = $validated['product_id'];

        // Find overlapping discounts
        $overlappingDiscounts = Discount::where('product_id', $productId)
            ->where(function($query) use ($startDate, $endDate) {
                // Check if new discount overlaps with existing ones
                $query->where(function($q) use ($startDate, $endDate) {
                    // Existing discount starts before new one ends AND ends after new one starts
                    $q->where('start_date', '<', $endDate)
                      ->where('end_date', '>', $startDate);
                });
            })
            ->get();

        if ($overlappingDiscounts->isNotEmpty()) {
            // Show warning with overlapping discounts info
            $overlappingCount = $overlappingDiscounts->count();
            $overlappingIds = $overlappingDiscounts->pluck('id')->toArray();
            
            // Store overlapping info in session for the view
            session()->flash('overlapping_discounts', [
                'count' => $overlappingCount,
                'ids' => $overlappingIds,
                'product_id' => $productId,
                'new_start_date' => $startDate->format('d/m/Y H:i'),
                'new_end_date' => $endDate->format('d/m/Y H:i'),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('warning', __('admin.overlapping_discounts_warning', ['count' => $overlappingCount]))
                ->with('overlap_confirmation', true);
        }

        // No overlapping discounts, create the new one
        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', __('admin.discount_created'));
    }

    public function storeWithOverride(Request $request)
    {
        // This method is called when user confirms to delete overlapping discounts
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'override_existing' => 'boolean',
        ]);

        // Convert fixed amount to percentage if provided
        if (!empty($validated['fixed_amount'])) {
            $product = Product::find($validated['product_id']);
            
            if (!$product) {
                return redirect()->back()
                    ->withErrors(['product_id' => 'Produit non trouvé.'])
                    ->withInput();
            }
            
            if ($product->price <= 0) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le prix du produit doit être supérieur à 0 pour calculer un pourcentage.'])
                    ->withInput();
            }
            
            // Calculate percentage from fixed amount
            $percentage = ($validated['fixed_amount'] / $product->price) * 100;
            
            // Ensure percentage doesn't exceed 100%
            if ($percentage > 100) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le montant fixe ne peut pas dépasser le prix du produit (100%).'])
                    ->withInput();
            }
            
            $validated['discount_percentage'] = $percentage;
        }
        
        unset($validated['fixed_amount']);

        $validated['is_active'] = $request->has('is_active');
        $validated['type'] = 'product';
        $validated['category_id'] = null;

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $productId = $validated['product_id'];

        // Delete overlapping discounts if user confirmed
        if ($request->has('override_existing')) {
            $deletedCount = Discount::where('product_id', $productId)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->where(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<', $endDate)
                          ->where('end_date', '>', $startDate);
                    });
                })
                ->delete();

            session()->flash('deleted_overlapping', $deletedCount);
        }

        // Create the new discount
        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', __('admin.discount_created'));
    }

    public function edit(Discount $discount)
    {
        // Calculate fixed amount from percentage for display
        $discount->fixed_amount = null;
        if ($discount->product && $discount->discount_percentage > 0) {
            $discount->fixed_amount = $discount->product->price * ($discount->discount_percentage / 100);
        }
        
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // Ensure at least one discount type is provided
        if (empty($validated['discount_percentage']) && empty($validated['fixed_amount'])) {
            return redirect()->back()
                ->withErrors(['discount_percentage' => 'Veuillez entrer soit un pourcentage, soit un montant fixe.'])
                ->withInput();
        }

        // Convert fixed amount to percentage if provided
        if (!empty($validated['fixed_amount'])) {
            $product = $discount->product;
            
            if (!$product) {
                return redirect()->back()
                    ->withErrors(['general' => __('admin.product_not_found')])
                    ->withInput();
            }
            
            if ($product->price <= 0) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le prix du produit doit être supérieur à 0 pour calculer un pourcentage.'])
                    ->withInput();
            }
            
            // Calculate percentage from fixed amount
            $percentage = ($validated['fixed_amount'] / $product->price) * 100;
            
            // Ensure percentage doesn't exceed 100%
            if ($percentage > 100) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le montant fixe ne peut pas dépasser le prix du produit (100%).'])
                    ->withInput();
            }
            
            $validated['discount_percentage'] = $percentage;
        }
        
        // Remove fixed_amount from validated data since we only store percentage
        unset($validated['fixed_amount']);

        $validated['is_active'] = $request->has('is_active');

        // For update, we need to exclude the current discount from overlap check
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $productId = $discount->product_id;

        // Check for overlapping discounts (excluding current one)
        $overlappingDiscounts = Discount::where('product_id', $productId)
            ->where('id', '!=', $discount->id)
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<', $endDate)
                      ->where('end_date', '>', $startDate);
                });
            })
            ->get();

        if ($overlappingDiscounts->isNotEmpty()) {
            $overlappingCount = $overlappingDiscounts->count();
            $overlappingIds = $overlappingDiscounts->pluck('id')->toArray();
            
            session()->flash('overlapping_discounts_update', [
                'count' => $overlappingCount,
                'ids' => $overlappingIds,
                'discount_id' => $discount->id,
                'new_start_date' => $startDate->format('d/m/Y H:i'),
                'new_end_date' => $endDate->format('d/m/Y H:i'),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('warning', __('admin.overlapping_discounts_warning', ['count' => $overlappingCount]))
                ->with('overlap_confirmation', true);
        }

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', __('admin.discount_updated'));
    }

    public function updateWithOverride(Request $request, Discount $discount)
    {
        // This method is called when user confirms to delete overlapping discounts during update
        $validated = $request->validate([
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'override_existing' => 'boolean',
        ]);

        // Convert fixed amount to percentage if provided
        if (!empty($validated['fixed_amount'])) {
            $product = $discount->product;
            
            if (!$product) {
                return redirect()->back()
                    ->withErrors(['general' => __('admin.product_not_found')])
                    ->withInput();
            }
            
            if ($product->price <= 0) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le prix du produit doit être supérieur à 0 pour calculer un pourcentage.'])
                    ->withInput();
            }
            
            $percentage = ($validated['fixed_amount'] / $product->price) * 100;
            
            if ($percentage > 100) {
                return redirect()->back()
                    ->withErrors(['fixed_amount' => 'Le montant fixe ne peut pas dépasser le prix du produit (100%).'])
                    ->withInput();
            }
            
            $validated['discount_percentage'] = $percentage;
        }
        
        unset($validated['fixed_amount']);

        $validated['is_active'] = $request->has('is_active');

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $productId = $discount->product_id;

        // Delete overlapping discounts if user confirmed
        if ($request->has('override_existing')) {
            $deletedCount = Discount::where('product_id', $productId)
                ->where('id', '!=', $discount->id)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->where(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<', $endDate)
                          ->where('end_date', '>', $startDate);
                    });
                })
                ->delete();

            session()->flash('deleted_overlapping', $deletedCount);
        }

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', __('admin.discount_updated'));
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', __('admin.discount_deleted'));
    }
}