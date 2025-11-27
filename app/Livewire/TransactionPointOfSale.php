<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Member;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TransactionPointOfSale extends Component
{
    // State Keranjang
    public $cart = [];
    public $receiptData = []; 
    public $total = 0;
    public $totalItems = 0;

    // Pembayaran
    public $paidAmount = 0;
    public $changeAmount = 0;
    public $paymentMethod = 'Cash';

    // Member & Diskon
    public $memberId = null;
    public $memberPhone = '';
    public $memberData = null;
    public $discountAmount = 0;

    // Pencarian Produk
    public $productSearch = '';
    public $searchResult = [];
    public $categoryId = null; // Filter kategori

    // Modal
    public $isPaymentModalOpen = false;
    public $printedTransactionId = null;

    // Data Pengaturan
    public $discountSettings = [];

    // State Riwayat
    public $dailyTransactions = [];

    public function mount()
    {
        $this->searchResult = collect();
        $this->loadSettings();
        $this->calculateTotal();
        $this->loadDailyTransactions();
    }

    public function loadSettings()
    {
        $this->discountSettings = Setting::getSettings();
    }

    // --- Pencarian Produk ---
    public function updatedProductSearch($value)
    {
        if (strlen($value) >= 2) {
            $query = Product::where('name', 'like', '%' . $value . '%')->where('stock', '>', 0);
            if ($this->categoryId) {
                $query->where('category_id', $this->categoryId);
            }
            // Tambahkan ->get() untuk mengembalikan collection
            $this->searchResult = $query->limit(5)->get();
        } else {
            $this->searchResult = collect(); // Pastikan selalu collection
        }
    }

    public function updatedCategoryId()
    {
        $this->searchResult = [];
        $this->updatedProductSearch($this->productSearch);
    }

    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->stock <= 0) {
            session()->flash('error', 'Produk tidak ditemukan atau stok habis.');
            return;
        }

        $currentQty = $this->cart[$productId]['qty'] ?? 0;
        if (($currentQty + 1) > $product->stock) {
            session()->flash('error', 'Stok tidak mencukupi.');
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->updateCartQuantity($productId, $currentQty + 1);
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'max_stock' => $product->stock,
                'qty' => 1,
                'image' => $product->image, // Tambahkan baris ini
            ];
            $this->productSearch = '';
            $this->searchResult = [];
        }

        $this->calculateTotal();
    }

    // --- Manajemen Keranjang ---
    public function updateCartQuantity($productId, $newQty)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $newQty = (int) $newQty;

        if ($newQty <= 0) {
            unset($this->cart[$productId]);
        } elseif ($newQty <= $product->stock) {
            $this->cart[$productId]['qty'] = $newQty;
        } else {
            session()->flash('error', 'Stok tidak mencukupi untuk item ini.');
            $this->cart[$productId]['qty'] = $product->stock;
        }

        $this->calculateTotal();
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    // -- Riwayat Transaksi --
    public function loadDailyTransactions()
    {
        $query = Transaction::with(['user', 'member'])
            ->whereDate('created_at', today());

        if (Auth::user()->role === 'kasir') {
            // Jika kasir, hanya tampilkan transaksinya sendiri
            $query->where('user_id', Auth::id());
        }

        $this->dailyTransactions = $query->orderBy('created_at', 'desc')->limit(5)->get();
    }

    // --- Member & Diskon ---
    public function findMember()
    {
        $this->memberData = null;
        $this->memberId = null;

        if (empty($this->memberPhone)) {
            session()->flash('error_member', 'Nomor HP tidak boleh kosong.');
            $this->calculateTotal();
            return;
        }

        $member = Member::where('phone', $this->memberPhone)->first();

        if ($member && $member->is_active) {
            $this->memberData = $member;
            $this->memberId = $member->id;
            session()->flash('message_member', 'Member berhasil ditemukan: ' . $member->name);
        } elseif ($member && !$member->is_active) {
            session()->flash('error_member', 'Member ditemukan, tetapi statusnya diblokir.');
        } else {
            session()->flash('error_member', 'Member tidak ditemukan.');
        }

        $this->calculateTotal();
    }

    public function removeMember()
    {
        $this->memberData = null;
        $this->memberId = null;
        $this->memberPhone = '';
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $subtotal = 0;
        $this->totalItems = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $this->totalItems += $item['qty'];
        }

        $this->total = $subtotal;
        $this->discountAmount = 0;

        // Apply Diskon (Hanya untuk Member)
        if ($this->memberId && $this->memberData) {
            $settings = $this->discountSettings;
            $discount = 0;

            // 1. Diskon Tetap Member (Persen)
            $memberDiscountPercent = (int) ($settings['member_discount_percent'] ?? 0);
            if ($memberDiscountPercent > 0) {
                $discount += $subtotal * ($memberDiscountPercent / 100);
            }

            // 2. Diskon Minimal Belanja (Rupiah)
            $minPurchaseThreshold = (int) ($settings['discount_min_spend'] ?? 0);
            $minPurchaseDiscountAmount = (int) ($settings['discount_min_spend_amount'] ?? 0);

            if ($subtotal >= $minPurchaseThreshold) {
                $discount += $minPurchaseDiscountAmount;
            }

            $this->discountAmount = round($discount);
            $this->total = max(0, $subtotal - $this->discountAmount);
        }

        $this->changeAmount = 0;
    }

    // --- Pembayaran ---
    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja kosong.');
            return;
        }
        $this->isPaymentModalOpen = true;
        $this->paidAmount = $this->total;
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->paidAmount = max(0, (int) $this->paidAmount);
        $this->changeAmount = $this->paidAmount - $this->total;
    }

    public function processTransaction()
    {
        if ($this->changeAmount < 0) {
            session()->flash('error_payment', 'Jumlah pembayaran kurang. Silakan periksa kembali.');
            return;
        }

        if (empty($this->cart)) {
            session()->flash('error_payment', 'Keranjang kosong.');
            return;
        }

        DB::beginTransaction();
        try {
            $subtotal_cart = array_sum(array_map(fn($item) => $item['price'] * $item['qty'], $this->cart));

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'member_id' => $this->memberId,
                'total_items' => $this->totalItems,
                'subtotal' => $subtotal_cart,
                'discount_amount' => $this->discountAmount,
                'grand_total' => $this->total,
                'paid_amount' => $this->paidAmount,
                'change_amount' => $this->changeAmount,
                'payment_method' => $this->paymentMethod,
            ]);

            foreach ($this->cart as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                Product::where('id', $item['id'])->decrement('stock', $item['qty']);

                StockMovement::create([
                    'product_id' => $item['id'],
                    'quantity' => -$item['qty'], // Negatif karena keluar
                    'type' => 'out',
                    'source' => 'transaction_' . $transaction->id,
                ]);
            }

            $this->printedTransactionId = $transaction->id;

            // Simpan data untuk struk SEBELUM reset
            $this->receiptData = [
                'items' => $this->cart,
                'paidAmount' => $this->paidAmount,
                'changeAmount' => $this->changeAmount,
                'total' => $this->total,
                'discountAmount' => $this->discountAmount,
                'totalItems' => $this->totalItems,
                'memberName' => $this->memberData ? $this->memberData->name : null, // Tambahkan baris ini
            ];

            DB::commit();

            $this->resetPOS(false);
            $this->isPaymentModalOpen = false;
            $this->refreshDailyTransactions();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
            $this->isPaymentModalOpen = false;
        }
    }

    // --- Helper ---
    public function resetPOS($fullReset = true)
    {
        $this->cart = [];
        $this->total = 0;
        $this->totalItems = 0;
        $this->paidAmount = 0;
        $this->changeAmount = 0;
        $this->memberId = null;
        $this->memberPhone = '';
        $this->memberData = null;
        $this->discountAmount = 0;
        $this->productSearch = '';
        $this->searchResult = [];
        $this->resetValidation();

        if ($fullReset) {
            $this->printedTransactionId = null;
        }
    }

    public function refreshDailyTransactions()
    {
        $this->loadDailyTransactions();
    }

    public function closeReceiptModal()
    {
        $this->resetPOS(true);
        $this->isPaymentModalOpen = false;
        $this->receiptData = [];
    }

    public function render()
    {
        $categories = Category::all();
        return view('livewire.transaction-point-of-sale', [
            'categories' => $categories
        ]);
    }
}
