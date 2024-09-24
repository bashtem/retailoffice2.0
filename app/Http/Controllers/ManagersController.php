<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\EnumController as Enum;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TopSalesStock;
use App\Exports\SaleItems;
use App\Exports\SaleItemsSummary;
use App\Exports\Sales;
use App\Exports\SalesItemsByUnitPrice;
use App\Exports\DailySalesSummary;
use App\Exports\AllItems;
use App\Exports\CancledItems;
use App\Exports\CustomerAmount;
use App\Exports\CustomerTicket;
use App\Exports\CustomerVolume;
use App\Exports\ItemQtyHistory;
use App\Exports\PurchasedItems;
use App\Exports\TotalSummary;
use App\Http\Middleware\Admin;
use App\Models\Credit;
use App\Models\Credit_log;
use App\Models\Credit_order;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Discount_item;
use App\Models\Discount_log;
use App\Models\Discount_paid_log;
use App\Models\Item;
use App\Models\Item_category;
use App\Models\Item_price;
use App\Models\Item_price_log;
use App\Models\Item_qty;
use App\Models\Item_qty_log;
use App\Models\Merchant_store;
use App\Models\Order;
use App\Models\Payment_type;
use App\Models\Purchase_order;
use App\Models\Purchase_order_item;
use App\Models\Qty_type;
use App\Models\Quantity_conversion;
use App\Models\Removed_item;
use App\Models\Supplier;
use App\Models\Transaction_type;
use App\Models\Transfer_access;
use App\Models\Transfer_item;
use App\Models\Transfer_order;
use App\Models\User;
use App\Models\User_role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ManagersController extends Controller implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(Admin::class, except: ['fetchConvertData', 'confirmTransferDatas']),
        ];
    }

    public function _index($token)
    {
        return $token;
    }

    public function dashboard()
    {
        return view('manager.dashboard');
    }
    public function inventory()
    {
        return view('manager.inventory');
    }

    public function managerTransfer()
    {
        return view('manager.transfer');
    }

    public function manageUsers()
    {
        return view('manager.manageUsers');
    }

    public function report()
    {
        return view('manager.report');
    }

    public function history()
    {
        return view('manager.history');
    }
    public function customers()
    {
        return view('manager.customers');
    }

    public function purchase()
    {
        return view('manager.purchase');
    }

    public function items()
    {
        $qtyType = Qty_type::all();
        return view('manager.items', ["qtyType" => $qtyType]);
    }

    public function suppliers()
    {
        $allSuppliers = Supplier::where(['merchant_id' => Auth::user()->merchant_id])->orderBy('sup_id', "DESC")->get();
        return view('manager.suppliers', ["suppliers" => $allSuppliers]);
    }

    public function purchaseData()
    {
        $suppliers = Supplier::where(['merchant_id' => Auth::user()->merchant_id])->orderBy('sup_company_name', 'ASC')->get();
        $paymentTypes = Payment_type::orderBy('payment_desc', 'ASC')->get();
        $categories = Item_category::with('items')->orderBy('cat_desc', "ASC")->get();
        $qtyTypes = Qty_type::orderBy('qty_desc', 'DESC')->get();
        $datas = [$suppliers, $paymentTypes, $categories, $qtyTypes];
        return $datas;
    }

    public function itemsData()
    {
        $categories = Item_category::with(['items' => function ($query) {
            $query->where('merchant_id', Auth::user()->merchant_id);
        }, 'items.item_price' => function ($query) {
            $query->where('item_prices.store_id', Auth::user()->store_id);
        }, 'items.item_price.qty_type'])->orderBy('cat_desc', "ASC")->get();
        return $categories;
    }

    public function addSupplier(Request $req)
    {
        $supplier = new Supplier();
        $supplier->merchant_id = Auth::user()->merchant_id;
        $supplier->store_id = Auth::user()->store_id;
        $supplier->sup_company_name = $req->input('companyName');
        $supplier->sup_contact_name = $req->input('contactName');
        $supplier->sup_mobile = $req->input('mobileNumber');
        $supplier->sup_mail = $req->input('mail');
        $supplier->sup_address = $req->input('address');
        $supplier->sup_website = $req->input('website');
        $supplier->save();
        return redirect("suppliers");
    }

    public function addItem(Request $req)
    {
        DB::beginTransaction();
        $qtyList = array();
        $priceList = array();
        try {
            $stores = Merchant_store::where('merchant_id', Auth::user()->merchant_id)->get();
            $qtyTypes = Qty_type::all();
            $item = new Item();
            $item->merchant_id = Auth::user()->merchant_id;
            $item->store_id = Auth::user()->store_id;
            $item->item_name = $req->input('itemName');
            $item->category_id = $req->input('itemCategory');
            $item->item_desc = $req->input('itemDescription');
            $item->save();

            foreach ($stores as $store) {
                foreach ($qtyTypes as $qtyType) {
                    $qtyList[] = ['merchant_id' => Auth::user()->merchant_id, 'store_id' => $store->store_id, 'qty_id' => $qtyType->qty_id, 'item_id' => $item->item_id, 'quantity' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                    $priceList[] = ['merchant_id' => Auth::user()->merchant_id, 'store_id' => $store->store_id, 'qty_id' => $qtyType->qty_id, 'item_id' => $item->item_id, 'price' => 0, 'min_price' => 0, 'max_price' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                }
            }
            Item_qty::insert($qtyList);
            Item_price::insert($priceList);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return redirect("items")->with('info', "modalNotificate('Item Added!')");
    }

    public function savePurchase(Request $req)
    {
        $data = $req[0]['data'];
        $purchaseOrder = array(
            "qty_id" => $req[0]['qtyId'],
            "supplier_id" => $req[0]['supplierId'],
            "merchant_id" => Auth::user()->merchant_id,
            "store_id" => Auth::user()->store_id,
            "user_id" => Auth::user()->user_id,
            "payment_id" => $req[0]['paymentId'],
            "purchase_time" => $req[0]['purchaseTime'],
            "purchase_date" => date("Y/m/d"),
            "purchase_note" => isset($req[0]['purchaseNote']) ? $req[0]['purchaseNote'] : '',
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        );
        DB::beginTransaction();
        try {
            $dbPurchaseOrder = DB::table('purchase_orders')->insertGetId($purchaseOrder, 'purchase_id');
            for ($x = 0; $x < count($data); $x++) {
                $purchaseOrderItems[]  =  array(
                    "item_id" => $data[$x]['itemId'],
                    "purchase_id" => $dbPurchaseOrder,
                    "purchase_qty" => $data[$x]['qty'],
                    "purchase_price" => $data[$x]['price'],
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                );
            }

            Purchase_order_Item::insert($purchaseOrderItems);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(["success" => true]);
    }

    public function fetchPrice($itemId, $qtyId)
    {
        $sPrice = Item_price::where(['item_id' => $itemId, 'qty_id' => $qtyId, 'store_id' => Auth::user()->store_id])->first();
        $qty = Item_qty::with(['qty_type'])->where(['item_id' => $itemId, 'qty_id' => $qtyId, 'store_id' => Auth::user()->store_id])->first();
        $dbData = (DB::select("SELECT * FROM purchase_order_items WHERE item_id = :itemId AND purchase_id IN (SELECT purchase_id FROM purchase_orders WHERE qty_id = :qtyId AND store_id = :storeID) ORDER BY purchase_item_id DESC LIMIT 1", ["itemId" => $itemId, "qtyId" => $qtyId, 'storeID' => Auth::user()->store_id]));
        $lastPurchasePrice['purPrice'] = (count($dbData) <= 0) ?  "" : $dbData[0];
        $lastPurchasePrice['qty'] = $qty;
        $lastPurchasePrice['sPrice'] = $sPrice;
        return $lastPurchasePrice;
    }

    public function updatePrice(Request $req)
    {
        DB::beginTransaction();
        try {
            $itemId =  $req['itemId'];
            $qtyId =  $req['qtyType'];
            $itemPrice = Item_price::where(['item_id' => $itemId, "qty_id" => $qtyId, 'store_id' => Auth::user()->store_id])->first();
            $itemPriceLog = new Item_price_log();
            $itemPriceLog->store_id = Auth::user()->store_id;
            $itemPriceLog->qty_id = $qtyId;
            $itemPriceLog->user_id = Auth::user()->user_id;
            $itemPriceLog->item_id = $itemId;
            $itemPriceLog->date = date("Y/m/d");
            $itemPriceLog->time = $req['time'];

            $itemPriceLog->old_price = $itemPrice->price;
            $itemPriceLog->old_min_price = $itemPrice->min_price;
            $itemPriceLog->old_max_price = $itemPrice->max_price;
            $itemPriceLog->new_price = $req['salePrice'];
            $itemPriceLog->new_min_price = $req['minPrice'] ?? 0;
            $itemPriceLog->new_max_price = $req['maxPrice'] ?? 0;
            $itemPriceLog->updated_at = Carbon::now();
            $itemPriceLog->save();

            $itemPrice->price = $req['salePrice'];
            $itemPrice->min_price = $req['minPrice'] ?? 0;
            $itemPrice->max_price = $req['maxPrice'] ?? 0;
            $itemPrice->updated_at = Carbon::now();
            $itemPrice->save();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json('Price Updated');
    }

    public static function fetchConvertData()
    {
        $categories = Item_category::with('items')->orderBy('cat_desc', "ASC")->get();
        $qtyTypes = Qty_type::orderBy('qty_desc', 'DESC')->get();
        return ['items' => $categories, 'qtyTypes' => $qtyTypes];
    }

    public function saveQtyConversion(Request $req)
    {
        DB::beginTransaction();
        try {
            $flagExist = DB::table("quantity_conversions")->where([["item_id", '=', $req->item_id], ["initial_qty_id", '=', $req->initial_qty_id], ["converted_qty_id", '=', $req->converted_qty_id], ['store_id', '=', Auth::user()->store_id]])->get();
            if (!$flagExist) {
                $req['created_at'] = Carbon::now();
                $req['updated_at'] = Carbon::now();
                $req['merchant_id'] = Auth::user()->merchant_id;
                $req['store_id'] = Auth::user()->store_id;
                $id = DB::table("quantity_conversions")->insertGetId($req->toArray(), "conversion_id");
                $req['conversion_id'] = $id;
                $req['user_id'] = Auth::user()->user_id;
                $req['conversion_date'] = date("Y/m/d");
                $req['conversion_time'] = date("h:i:s");
                DB::table("quantity_conversion_logs")->insert($req->toArray());
                $flag = true;
            } else {
                $flag = false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        if ($flag) {
            return response()->json(["response" => "Quantity Conversion Added"]);
        } else {
            return response()->json(["response" => "Quantity Conversion Exist"]);
        }
    }

    public function fecthQtyConversion()
    {
        $datas = Quantity_conversion::with(["items", "srcQtyType", "cnvQtyType"])->where(['store_id' => Auth::user()->store_id])->orderBy("conversion_id", "DESC")->get();
        return $datas;
    }

    public function delQtyConversion(Request $req)
    {
        try {
            Quantity_conversion::where([["item_id", '=', $req['item_id']], ["initial_qty_id", '=', $req['initial_qty_id']], ["converted_qty_id", '=', $req['converted_qty_id']]])->where(['store_id' => Auth::user()->store_id])->delete();
        } catch (\Exception $e) {
            throw $e;
        }
        return response()->json("Delete Success");
    }

    public function transferAccess(Request $req)
    {
        DB::beginTransaction();
        try {
            $model = Transfer_access::where('user_id_assigned', $req['userIdAssigned'])->get();
            if ($model) {
                Transfer_access::where('user_id_assigned', $req['userIdAssigned'])->update(['user_id' => Auth::user()->user_id, 'transfer_count' => $req['transferCount'], 'user_id_assigned' => $req['userIdAssigned'], 'assigned_date' => date("Y/m/d"), 'assigned_time' => $req['assignedTime'], 'updated_at' => Carbon::now()]);
                $id = $model->access_id;
            } else {

                $id = DB::table('transfer_accesses')->insertGetId(['user_id' => Auth::user()->user_id, 'transfer_count' => $req['transferCount'], 'user_id_assigned' => $req['userIdAssigned'], 'assigned_date' => date("Y/m/d"), 'assigned_time' => $req['assignedTime'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
            DB::table('transfer-access_logs')->insert(['access_id' => $id, 'user_id' => Auth::user()->user_id, 'transfer_count' => $req['transferCount'], 'user_id_assigned' => $req['userIdAssigned'], 'assigned_date' => date("Y/m/d"), 'assigned_time' => $req['assignedTime'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return response()->json(["response" => 'Success']);
    }

    public function salesHistory($date)
    {
        return Order::with(['cus', 'payment', 'qty', 'items.item', 'user', 'discount'])->where(['order_date' => $date, 'store_id' => Auth::user()->store_id])->orderBy('order_id', 'DESC')->get();
    }

    public function dashboardData($date)
    {
        $orderSum = Order::where(function ($query) {
            $query->where('order_status', Enum::SUCCESS)->orWhere('order_status', Enum::PENDING);
        })->where(['order_date' => $date, 'store_id' => Auth::user()->store_id])->sum('order_total_amount');
        $orderCount = Order::where(function ($query) {
            $query->where('order_status', Enum::SUCCESS)->orWhere('order_status', Enum::PENDING);
        })->where(['order_date' => $date, 'store_id' => Auth::user()->store_id])->count('order_id');
        $purchaseOrder = Purchase_Order::where(['purchase_date' => $date, 'store_id' => Auth::user()->store_id])->count('purchase_id');
        $transferOrder = Transfer_Order::where('transfer_status', Enum::SUCCESS)->where(['transfer_date' => $date, 'store_id' => Auth::user()->store_id])->count('transfer_id');
        return ["salesAmount" => $orderSum, "salesCount" => $orderCount, "purchaseCount" => $purchaseOrder, "transferCount" => $transferOrder];
    }

    public function cancleSalesOrder($orderId, $time)
    {

        DB::beginTransaction();
        try {
            $order = Order::with('payment')->where('order_id', $orderId)->first();
            Order::where('order_id', $orderId)->update(['order_status' => Enum::CANCLED, "updated_at" => Carbon::now(), "cancled_date" => date("Y-m-d"), "cancled_time" => $time]);

            // Discount
            $discountLog = Discount_log::where('order_id', $orderId)->first();
            if (isset($discountLog)) {
                $discount = Discount::where('cus_id', $order->cus_id)->first();
                $newDiscount = ($discount->discount_credit) - ($discountLog->total_discount);
                Discount::where('cus_id', $order->cus_id)->update(['discount_credit' => $newDiscount, 'updated_at' => Carbon::now()]);
                Discount_log::where('order_id', $orderId)->update(['discount_status' => Enum::CANCLED, 'updated_at' => Carbon::now()]);
            }

            // Credit
            if ($order->payment->payment_desc == ENUM::CREDIT) {
                $credit = Credit::where('cus_id', $order->cus_id)->first();
                $creditOrder = Credit_Order::where('cus_id', $order->cus_id)->first();
                $newAvail = $credit->available_credit + $order->order_total_amount;
                $newOut = $credit->out_credit - $order->order_total_amount;
                Credit::where('cus_id', $order->cus_id)->update(['available_credit' => $newAvail, 'out_credit' => $newOut]);
                Credit_log::insert(["credit_order_id" => $creditOrder->credit_order_id, "user_id" => Auth::user()->user_id, "credit_id" => $credit->credit_id, "credit_log_status" => Enum::ORDER, "old_credit" => $credit->available_credit, "new_credit" => $newAvail, "credit_date" => date('Y-m-d'), "credit_time" => $time, "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
            }

            // Item Quantity
            $tranType = Transaction_type::where('trans_desc', Enum::ADD)->first();
            $orderItems = Order::with('items')->where('order_id', $orderId)->first();
            $qtyId = $orderItems->qty_id;
            $items = new Collection($orderItems->items);
            $items->map(function ($item) use ($qtyId, $tranType, $time) {
                $itemQty = Item_qty::where('item_id', $item->item_id)->where('qty_id', $qtyId)->where(['store_id' => Auth::user()->store_id])->first();
                $newQty = ($itemQty['quantity']) + ($item['quantity']);
                Item_qty::where('item_id', $item->item_id)->where('qty_id', $qtyId)->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $newQty, 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(["user_id" => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, "qty_id" => $qtyId, "item_id" => $item['item_id'], "old_qty" => $itemQty['quantity'], "new_qty" => $newQty, "trans_id" => $tranType->trans_id, "date" => date("Y-m-d"), "time" => $time, "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
            });
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => "SUCCESS"]);
    }

    public function transferHistory($date)
    {
        return Transfer_Order::with(['transfer_items.conversion.items', 'transfer_items.conversion.srcQtyType', 'transfer_items.conversion.cnvQtyType', 'user'])->where(['transfer_date' => $date, 'store_id' => Auth::user()->store_id])->orderBy('transfer_id', 'DESC')->get();
    }

    public function purchaseHistory($date)
    {
        return Purchase_Order::with(['Purchase_order_item.items', 'Supplier', 'Qty', 'user', 'payment'])->where(['purchase_date' => $date, 'store_id' => Auth::user()->store_id])->orderBy('purchase_id', 'DESC')->get();
    }

    public function canclePurchase($purchaseId, $itemId, $time)
    {

        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::PURCHASE)->first();
            $purOrder = Purchase_Order::where('purchase_id', $purchaseId)->first();
            $purItem = Purchase_order_Item::where('purchase_id', $purchaseId)->where('item_id', $itemId)->first();
            Purchase_order_Item::where('purchase_id', $purchaseId)->where('item_id', $itemId)->update(['user_id_cancled' => Auth::user()->user_id, 'purchase_status' => Enum::CANCLED, 'cancled_date' => date('Y-m-d'), 'cancled_time' => $time, 'updated_at' => Carbon::now()]);
            $successCount = Purchase_order_Item::where('purchase_status', Enum::SUCCESS)->where('purchase_id', $purchaseId)->count();
            if ($successCount < 1) { // UPDATE PURCHASE ORDER TO CANCLED
                Purchase_Order::where('purchase_id', $purchaseId)->update(['user_id_cancled' => Auth::user()->user_id, 'cancled_date' => date('Y-m-d'), 'cancled_time' => $time]);
            }
            if ($purItem->purchase_status == Enum::SUCCESS) {  // REMOVE ITEM QUANTITY THAT HAS BEEN ADDED TO STOCK.
                $itemQty = Item_qty::where('item_id', $itemId)->where('qty_id', $purOrder->qty_id)->where(['store_id' => Auth::user()->store_id])->first();
                $newQty = ($itemQty->quantity) - ($purItem->purchase_qty);
                Item_qty::where('item_id', $itemId)->where('qty_id', $purOrder->qty_id)->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $newQty, 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $purOrder->qty_id, 'item_id' => $itemId, 'old_qty' => $itemQty->quantity, 'new_qty' => $newQty, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function cancleAllPurchase($purchaseId, $time)
    {

        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::PURCHASE)->first();
            Purchase_Order::where('purchase_id', $purchaseId)->update(['user_id_cancled' => Auth::user()->user_id, 'cancled_date' => date('Y-m-d'), 'cancled_time' => $time]);
            $purOrder = Purchase_Order::with('Purchase_order_item')->where('purchase_id', $purchaseId)->first();
            $purItems = new Collection($purOrder->purchase_order_item);
            $purItems->map(function ($each) use ($purchaseId, $time, $purOrder, $tran) {
                Purchase_order_Item::where('purchase_id', $purchaseId)->where('item_id', $each['item_id'])->update(['user_id_cancled' => Auth::user()->user_id, 'purchase_status' => Enum::CANCLED, 'cancled_date' => date('Y-m-d'), 'cancled_time' => $time, 'updated_at' => Carbon::now()]);
                if ($each['purchase_status'] == Enum::SUCCESS) {  // REMOVE ITEM QUANTITY THAT HAS BEEN ADDED TO STOCK.
                    $itemQty = Item_qty::where('item_id', $each['item_id'])->where('qty_id', $purOrder->qty_id)->where(['store_id' => Auth::user()->store_id])->first();
                    $newQty = ($itemQty->quantity) - ($each['purchase_qty']);
                    Item_qty::where('item_id', $each['item_id'])->where('qty_id', $purOrder->qty_id)->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $newQty, 'updated_at' => Carbon::now()]);
                    Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $purOrder->qty_id, 'item_id' => $each['item_id'], 'old_qty' => $itemQty->quantity, 'new_qty' => $newQty, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            });
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function storeQty($itemId)
    {
        return Item::where('item_id', $itemId)->where(['merchant_id' => Auth::user()->merchant_id])->with(['item_qty.qty_type', 'item_qty' => function ($q) {
            $q->where(['store_id' => Auth::user()->store_id]);
        }])->first();
    }

    public function transferStock(Request $req)
    {
        DB::beginTransaction();
        try {
            $transactionType = Transaction_type::where('trans_desc', 'TRANSFER')->first();
            $transferId = DB::table('transfer_orders')->insertGetid(['user_id_transfer' => Auth::user()->user_id, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'transfer_status' => Enum::SUCCESS, 'user_id_confirmed' => Auth::user()->user_id, 'confirmed_date' => date('Y-m-d'), 'confirmed_time' => $req['order']['transfer_time'], 'transfer_date' => date('Y/m/d'), 'transfer_time' => $req['order']['transfer_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'transfer_id');
            for ($x = 0; $x < count($req['items']); $x++) {
                Transfer_Item::insert(['transfer_id' => $transferId, 'transfer_status' => Enum::SUCCESS, 'user_id_confirmed' => Auth::user()->user_id, 'confirmed_date' => date('Y-m-d'), 'confirmed_time' => $req['order']['transfer_time'], 'conversion_id' => $req['items'][$x]['conversion_id'], 'item_id' => $req['items'][$x]['item_id'], 'transfer_qty' => $req['items'][$x]['transfer_qty'], 'transferred_qty' => $req['items'][$x]['transferred_qty'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                $itemQtySrc = Item_qty::where([['item_id', '=', $req['items'][$x]['item_id']], ['qty_id', '=', $req['items'][$x]['src_qty_id']]])->where(['store_id' => Auth::user()->store_id])->first();
                $oldSrcQty = $itemQtySrc->quantity;
                $newSrcQty = ($oldSrcQty) - ($req['items'][$x]['transfer_qty']);
                $itemQtySrc->quantity = $newSrcQty;
                $itemQtySrc->updated_at = Carbon::now();
                $itemQtySrc->save();

                Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $req['items'][$x]['src_qty_id'], 'item_id' => $req['items'][$x]['item_id'], 'old_qty' => $oldSrcQty, 'new_qty' => $newSrcQty, 'trans_id' => $transactionType->trans_id, 'date' => date('Y/m/d'), 'time' => $req['order']['transfer_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                $itemQtyTrn = Item_qty::where([['item_id', '=', $req['items'][$x]['item_id']], ['qty_id', '=', $req['items'][$x]['trn_qty_id']]])->where(['store_id' => Auth::user()->store_id])->first();
                $oldQty = $itemQtyTrn->quantity;
                $newQty = ($oldQty) + ($req['items'][$x]['transferred_qty']);
                $itemQtyTrn->quantity = $newQty;
                $itemQtyTrn->updated_at = Carbon::now();
                $itemQtyTrn->save();

                Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $req['items'][$x]['trn_qty_id'], 'item_id' => $req['items'][$x]['item_id'], 'old_qty' => $oldQty, 'new_qty' => $newQty, 'trans_id' => $transactionType->trans_id, 'date' => date('Y/m/d'), 'time' => $req['order']['transfer_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return "Transfer Successfull";
    }

    public function cancleTransfer($transferId, $time)
    {
        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::TRANSFER)->first();
            $transferOrder = Transfer_Order::with('transfer_items.conversion')->where('transfer_id', $transferId)->first();
            Transfer_Order::where('transfer_id', $transferId)->update(['transfer_status' => Enum::CANCLED, "user_id_cancled" => Auth::user()->user_id, "cancled_date" => date('Y-m-d'), "cancled_time" => $time, 'updated_at' => Carbon::now()]);
            $transferItems = new Collection($transferOrder->transfer_items);

            $transferItems->map(function ($each) use ($transferId, $time, $tran, $transferOrder) {
                if ($each['transfer_status'] == Enum::SUCCESS || $each['transfer_status'] == Enum::PENDING) {
                    Transfer_Item::where('transfer_id', $transferId)->where('item_id', $each['item_id'])->update(['user_id_cancled' => Auth::user()->user_id, "cancled_date" => date("Y-m-d"), "cancled_time" => $time, "transfer_status" => Enum::CANCLED, "updated_at" => Carbon::now()]);

                    // Item Qty transfer
                    $initialItem = Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                    $qtyTransfer = ($initialItem->quantity) + ($each['transfer_qty']);
                    Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransfer, 'updated_at' => Carbon::now()]);
                    Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $each['conversion']['initial_qty_id'], 'item_id' => $each['item_id'], 'old_qty' => $initialItem->quantity, 'new_qty' => $qtyTransfer, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                    // Item Qty transferred
                    $finalItem = Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                    $qtyTransferred = ($finalItem->quantity) - ($each['transferred_qty']);
                    Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransferred, 'updated_at' => Carbon::now()]);
                    Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $each['conversion']['converted_qty_id'], 'item_id' => $each['item_id'], 'old_qty' => $finalItem->quantity, 'new_qty' => $qtyTransferred, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            });
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return 'Transfer Cancled!';
    }

    public function confirmTransfer($transferId, $time)
    {
        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::TRANSFER)->first();
            $transferOrder = Transfer_Order::with('transfer_items.conversion')->where('transfer_id', $transferId)->first();
            Transfer_Order::where('transfer_id', $transferId)->update(['transfer_status' => Enum::SUCCESS, "user_id_confirmed" => Auth::user()->user_id, "confirmed_date" => date('Y-m-d'), "confirmed_time" => $time, 'updated_at' => Carbon::now()]);
            $transferItems = new Collection($transferOrder->transfer_items);

            $transferItems->map(function ($each) use ($transferId, $time, $tran, $transferOrder) {
                if ($each['transfer_status'] == Enum::PENDING) {
                    Transfer_Item::where('transfer_id', $transferId)->where('item_id', $each['item_id'])->update(['user_id_confirmed' => Auth::user()->user_id, "confirmed_date" => date("Y-m-d"), "confirmed_time" => $time, "transfer_status" => Enum::SUCCESS, "updated_at" => Carbon::now()]);
                    // Item Qty transfer
                    $initialItem = Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                    $qtyTransfer = ($initialItem->quantity) - ($each['transfer_qty']);
                    Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransfer, 'updated_at' => Carbon::now()]);
                    Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $each['conversion']['initial_qty_id'], 'item_id' => $each['item_id'], 'old_qty' => $initialItem->quantity, 'new_qty' => $qtyTransfer, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                    // Item Qty transferred
                    $finalItem = Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                    $qtyTransferred = ($finalItem->quantity) + ($each['transferred_qty']);
                    Item_qty::where('item_id', $each['item_id'])->where('qty_id', $each['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransferred, 'updated_at' => Carbon::now()]);
                    Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $each['conversion']['converted_qty_id'], 'item_id' => $each['item_id'], 'old_qty' => $finalItem->quantity, 'new_qty' => $qtyTransferred, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            });
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return 'Transfer Successful';
    }

    public function confirmTransferDatas()
    {
        return Transfer_Order::whereDate('updated_at', Carbon::today())->where(['store_id' => Auth::user()->store_id])->orWhere('transfer_status', Enum::PENDING)->with(['user', 'transfer_items.item', 'transfer_items.conversion.srcQtyType', 'transfer_items.conversion.cnvQtyType'])->orderBy('transfer_id', 'DESC')->get();
    }

    public function cancleEachTransfer($transferId, $itemId, $time)
    {
        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::TRANSFER)->first();
            $transferItem = Transfer_Item::with('conversion')->where('transfer_id', $transferId)->where('item_id', $itemId)->first();

            $countSuccess = Transfer_Item::where('transfer_id', $transferId)->where('transfer_status', Enum::SUCCESS)->count();
            if ($countSuccess == 1) {
                Transfer_Order::where('transfer_id', $transferId)->update(['transfer_status' => Enum::CANCLED, 'updated_at' => Carbon::now()]);
            } else {
                Transfer_Order::where('transfer_id', $transferId)->update(['transfer_status' => Enum::SUCCESS, 'updated_at' => Carbon::now()]);
            }

            Transfer_Item::where('transfer_id', $transferId)->where('item_id', $itemId)->update(['user_id_cancled' => Auth::user()->user_id, "cancled_date" => date("Y-m-d"), "cancled_time" => $time, "transfer_status" => Enum::CANCLED, "updated_at" => Carbon::now()]);
            if ($transferItem->transfer_status == Enum::SUCCESS) {   /// If item is not on pending
                // Item Qty transfer
                $initialItem = Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                $qtyTransfer = ($initialItem->quantity) + ($transferItem->transfer_qty);
                Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransfer, 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'qty_id' => $transferItem['conversion']['initial_qty_id'], 'item_id' => $itemId, 'old_qty' => $initialItem->quantity, 'new_qty' => $qtyTransfer, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                // Item Qty transferred
                $finalItem = Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                $qtyTransferred = ($finalItem->quantity) - ($transferItem->transferred_qty);
                Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransferred, 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $transferItem['conversion']['converted_qty_id'], 'item_id' => $itemId, 'old_qty' => $finalItem->quantity, 'new_qty' => $qtyTransferred, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return "Transfer Cancled!";
    }

    public function confirmEachTransfer($transferId, $itemId, $time)
    {
        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::TRANSFER)->first();
            $transferItem = Transfer_Item::with('conversion')->where('transfer_id', $transferId)->where('item_id', $itemId)->first();

            Transfer_Order::where('transfer_id', $transferId)->update(['transfer_status' => Enum::SUCCESS, 'updated_at' => Carbon::now()]);

            Transfer_Item::where('transfer_id', $transferId)->where('item_id', $itemId)->update(['user_id_cancled' => Auth::user()->user_id, "confirmed_date" => date("Y-m-d"), "confirmed_time" => $time, "transfer_status" => Enum::SUCCESS, "updated_at" => Carbon::now()]);
            // Item Qty transfer
            $initialItem = Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
            $qtyTransfer = ($initialItem->quantity) - ($transferItem->transfer_qty);
            Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['initial_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransfer, 'updated_at' => Carbon::now()]);
            Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $transferItem['conversion']['initial_qty_id'], 'item_id' => $itemId, 'old_qty' => $initialItem->quantity, 'new_qty' => $qtyTransfer, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            // Item Qty transferred
            $finalItem = Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
            $qtyTransferred = ($finalItem->quantity) + ($transferItem->transferred_qty);
            Item_qty::where('item_id', $itemId)->where('qty_id', $transferItem['conversion']['converted_qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyTransferred, 'updated_at' => Carbon::now()]);
            Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $transferItem['conversion']['converted_qty_id'], 'item_id' => $itemId, 'old_qty' => $finalItem->quantity, 'new_qty' => $qtyTransferred, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return "Transfer Successful";
    }

    public function Users()
    {
        $datas['customers'] = Customer::where(['store_id' => Auth::user()->store_id])->count();
        $datas['suppliers'] = Supplier::where(['merchant_id' => Auth::user()->merchant_id])->count();
        $datas['managers'] = User::whereHas('userRole', function ($query) {
            $query->where('role_level', Enum::ADMIN);
        })->where(['store_id' => Auth::user()->store_id])->count();
        $datas['users'] = User::where(['store_id' => Auth::user()->store_id])->count();
        $datas['items'] = Item::where(['merchant_id' => Auth::user()->merchant_id])->count();
        return $datas;
    }

    public function fetchDashQty($qtyId)
    {
        $datas['item'] =  Item::where(['merchant_id' => Auth::user()->merchant_id])->get()->map(function ($i) {
            return $i->item_name;
        });
        $datas['qty'] = Item_qty::where(['store_id' => Auth::user()->store_id])->where('qty_id', $qtyId)->get()->map(function ($j) {
            return $j->quantity;
        });

        return $datas;
    }

    public function qtyTypes()
    {
        return Qty_type::orderBy('qty_desc', 'ASC')->get();
    }

    public function inventoryItems()
    {
        return Item::with('item_category')->where(['merchant_id' => Auth::user()->merchant_id])->get();
    }

    public function selectedInv($itemId)
    {
        return Item::with(['item_qty' => function ($query) {
            $query->where('store_id', Auth::user()->store_id);
        }, 'item_qty.qty_type', 'item_qty.itemPrice'])->where('item_id', $itemId)->where('store_id', Auth::user()->store_id)->first();
    }

    public function removeItem(Request $req)
    {
        DB::beginTransaction();
        try {
            $tran = Transaction_type::where('trans_desc', Enum::REMOVED)->first();
            Removed_item::insert(['qty_id' => $req->qtyId['qty_id'], 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'item_id' => $req->qtyId['item_id'], 'user_id' => Auth::user()->user_id, 'note' => $req->note, 'removal_date' => date('Y-m-d'), 'removal_time' => $req->time, 'quantity' => $req->qty, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $initialQty = Item_qty::where('item_id', $req->qtyId['item_id'])->where('qty_id', $req->qtyId['qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
            $qtyNew = ($initialQty->quantity) - ($req->qty);
            Item_qty::where('item_id', $req->qtyId['item_id'])->where('qty_id', $req->qtyId['qty_id'])->where(['store_id' => Auth::user()->store_id])->update(['quantity' => $qtyNew, 'updated_at' => Carbon::now()]);
            Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $req->qtyId['qty_id'], 'item_id' => $req->qtyId['item_id'], 'old_qty' => $initialQty->quantity, 'new_qty' => $qtyNew, 'trans_id' => $tran->trans_id, 'date' => date('Y-m-d'), 'time' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return "Success";
    }

    public function fetchUsers()
    {
        return User::with('userRole')->where(['store_id' => Auth::user()->store_id])->get();
    }

    public function userStatus($userid, $status)
    {
        $stat = ($status == 'true') ? Enum::ACTIVE : Enum::INACTIVE;
        User::where('user_id', $userid)->update(['status' => $stat]);
        return $stat;
    }

    public function fetchRoles()
    {
        return User_role::all();
    }

    public function newUser(Request $req)
    {
        $status = ($req->status == 'true') ? Enum::ACTIVE : Enum::INACTIVE;
        $password = Hash::make($req->password);
        try {
            User::insert(['name' => $req->name, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'email' => $req->email, 'phone' => $req->phone, 'username' => $req->username, 'password' => $password, 'role' => $req->role['role_id'], 'status' => $status, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062)
                return response()->json(['response' => "User Exits"]);
            else
                return response()->json(['response' => "User Registration Failed"]);
        }
        return response()->json(['response' => "User Registered"]);
    }

    public function editUser(Request $req)
    {
        $password = Hash::make($req->password);
        try {
            User::where('user_id', $req->userId)->update(['name' => $req->name, 'email' => $req->email, 'phone' => $req->phone, 'username' => $req->username, 'password' => $password, 'role' => $req->role['role_id'], 'updated_at' => Carbon::now()]);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062)
                return response()->json(['response' => "User Exits"]);
            else
                return response()->json(['response' => "Profile Update Failed"]);
        }
        return response()->json(['response' => "Profile Updated"]);
    }

    public function fetchCustomers()
    {
        return Customer::with('payment', 'registeredBy', 'credit', 'discount')->where(['store_id' => Auth::user()->store_id])->get();
    }

    public function fetchPayment()
    {
        return Payment_type::all();
    }

    public function addCustomer(Request $req)
    {
        DB::beginTransaction();
        try {
            $id = Customer::insertGetId(['registered_by_user_id' => Auth::user()->user_id, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'cus_name' => $req->name, 'cus_mobile' => $req->phone, 'cus_mail' => $req->email, 'cus_address' => $req->address, 'payment_id' => $req->payment['payment_id'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'cus_id');
            Credit::insert(['cus_id' => $id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            Discount::insert(['cus_id' => $id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\PDOException $e) {
            DB::rollback();
            if ($e->errorInfo[1] == 1062)
                return response()->json(['response' => "Customer Exits", "status" => false]);
            else
                return response()->json(['response' => "Customer Registration Failed", "status" => false]);
        }
        DB::commit();
        return response()->json(['response' => "Customer Added", "status" => true]);
    }

    public function editCustomer(Request $req)
    {
        try {
            Customer::where('cus_id', $req->id)->update(['cus_name' => $req->name, 'cus_mobile' => $req->phone, 'cus_mail' => $req->mail, 'cus_address' => $req->address, 'payment_id' => $req->pay['payment_id'], 'updated_at' => Carbon::now()]);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062)
                return response()->json(['response' => "Customer Exits"]);
            else
                return response()->json(['response' => "Profile Update Failed"]);
        }
        return response()->json(['response' => "Profile Updated"]);
    }

    public function creditProcess(Request $req)
    {
        DB::beginTransaction();
        try {
            $credit = Credit::where('cus_id', $req->cus_id)->first();
            if ($req->type == ENUM::CREDIT) {
                $new = $credit->available_credit + $req->amt;
                Credit::where('cus_id', $req->cus_id)->update(['available_credit' => $new, 'updated_at' => Carbon::now()]);
                Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::CREDIT, 'old_credit' => $credit->available_credit, 'new_credit' => $new, 'credit_date' => date("Y-m-d"), 'credit_time' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
            if ($req->type == ENUM::DEBIT) {
                $newdebit = $credit->available_credit - $req->amt;
                Credit::where('cus_id', $req->cus_id)->update(['available_credit' => $newdebit, 'updated_at' => Carbon::now()]);
                Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::DEBIT, 'old_credit' => $credit->available_credit, 'new_credit' => $newdebit, 'credit_date' => date("Y-m-d"), 'credit_time' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function creditOrders(Request $req)
    {
        return Credit_Order::where('cus_id', $req->cus_id)->where('credit_order_status', Enum::OUTSTANDING)->with('order', 'order.payment', 'order.qty')->orderBy('credit_order_id', 'DESC')->get();
    }

    public function payDebit(Request $req)
    {
        DB::beginTransaction();
        try {
            $credit = Credit::where('cus_id', $req->cus_id)->first();
            $avail = $credit->available_credit + $req->amount;
            $out = $credit->out_credit - $req->amount;
            Credit::where('cus_id', $req->cus_id)->update(['available_credit' => $avail, 'out_credit' => $out, 'updated_at' => Carbon::now()]);
            Credit_Order::where('cus_id', $req->cus_id)->where('credit_order_id', $req->creditOrderId)->update(['credit_order_status' => Enum::PAID, 'date_paid' => date('Y-m-d'), 'time_paid' => $req->time, 'updated_at' => Carbon::now()]);
            Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_order_id' => $req->creditOrderId, 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::PAID, 'old_credit' => $credit->available_credit, 'new_credit' => $avail, 'credit_date' => date("Y-m-d"), 'credit_time' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function fetchCredit(Request $req)
    {
        return credit::where('cus_id', $req->cusId)->first();
    }

    public function payDiscount(Request $req)
    {
        DB::beginTransaction();
        try {
            $cusDiscount = Discount::where('cus_id', $req->cusId)->first();
            $newDis = $cusDiscount->discount_credit - $req->amount;
            Discount::where('cus_id', $req->cusId)->update(['discount_credit' => $newDis, 'updated_at' => Carbon::now()]);
            Discount_paid_log::insert(['cus_id' => $req->cusId, 'user_id' => Auth::user()->user_id, 'paid_amount' => $req->amount, 'date_paid' => date("Y-m-d"), 'time_paid' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function fetchDiscountBal(Request $req)
    {
        return Discount::where('cus_id', $req->cusId)->first();
    }

    public function addDiscount(Request $req)
    {
        DB::beginTransaction();
        try {
            $existDiscount = Discount_item::where('item_id', $req->item['item_id'])->where('qty_id', $req->unit['qty_id'])->where('cus_id', $req->cusId)->count();
            if ($existDiscount >= 1) {
                return response()->json(['response' => 'Discount Exist!']);
            }
            Discount_Item::insert(['cus_id' => $req->cusId, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'user_id_enabled' => Auth::user()->user_id, 'item_id' => $req->item['item_id'], 'qty_id' => $req->unit['qty_id'], 'item_qty' => $req->qty, 'discount_amount' => $req->amount, 'enabled_date' => date('Y-m-d'), 'enabled_time' => $req->time, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function discountItems(Request $req)
    {
        return Discount_Item::where('cus_id', $req->cusId)->with('item', 'unit')->orderBy('discount_item_id', 'DESC')->get();
    }

    public function delDiscount(Request $req)
    {
        DB::beginTransaction();
        try {
            Discount_Item::where('item_id', $req->item)->where('qty_id', $req->qty)->where('cus_id', $req->cusId)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(['response' => 'Success']);
    }

    public function salesReport(Request $req)
    {

        $qty = Qty_type::all();

        $qtyId = $req->qtyId;

        // TOTAL FOR ALL SALES
        if (($req->reportType >= 2) && ($req->reportType <= 6)) {
            $totalQty = $totalAmount = $totalCost = $totalGrossProfit = 0;

            $totalsQuery = DB::select("SELECT order_items.quantity, (order_items.price * order_items.quantity) as amount, (order_items.cost_price * order_items.quantity) as cost, ((order_items.price * order_items.quantity) - (order_items.cost_price * order_items.quantity)) as gross_profit  FROM orders join order_items on orders.order_id = order_items.order_id  where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);

            (new Collection($totalsQuery))->each(function ($each) use (&$totalQty, &$totalAmount, &$totalCost, &$totalGrossProfit) {
                $totalQty += $each->quantity;
                $totalAmount += $each->amount;
                $totalCost += $each->cost;
                $totalGrossProfit += $each->gross_profit;
            });

            $totals = ["totalQty" => $totalQty, "totalAmount" => $totalAmount, "totalCost" => $totalCost, "totalGrossProfit" => $totalGrossProfit];
        }

        switch ($req->reportType) {
            case '1':
                $topSalesStock = DB::select("SELECT SUM(order_items.quantity) as totalQty, SUM((order_items.price)*(order_items.quantity)) as amount, order_items.price AS price, order_items.item_id, orders.qty_id, items.item_name, qty_types.qty_desc FROM order_items  JOIN orders ON order_items.order_id = orders.order_id JOIN items ON items.item_id = order_items.item_id JOIN qty_types ON orders.qty_id = qty_types.qty_id where orders.store_id = ? AND (date(order_items.created_at) between ? AND ?) AND orders.order_status !='CANCLED'  GROUP BY order_items.price, order_items.item_id  ORDER BY items.item_name", [Auth::user()->store_id, $req->fromDate, $req->toDate]);

                foreach ($qty as $val) {

                    $itemUnit[$val->qty_desc] =  (new Collection($topSalesStock))->filter(function ($each) use ($val) {
                        return $val->qty_id == $each->qty_id;
                    });

                    $totalAmount = $itemUnit[$val->qty_desc]->reduce(function ($total, $itm) {
                        return $total + $itm->amount;
                    });

                    $totalQty = $itemUnit[$val->qty_desc]->reduce(function ($total, $itm) {
                        return $total + $itm->totalQty;
                    });

                    $summary[] = ["unit" => $val->qty_desc, "totalAmount" => $totalAmount, "totalQty" => $totalQty];
                }
                return ["topSales" => $topSalesStock, "summary" => $summary];
                break;

            case '2':
                $saleItems = DB::select("SELECT orders.order_date, orders.order_no, customers.cus_name, users.name, items.item_name, qty_types.qty_desc,  order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as amount, (order_items.cost_price * order_items.quantity) as cost, ((order_items.price * order_items.quantity) - (order_items.cost_price * order_items.quantity)) as gross_profit  FROM orders join order_items on orders.order_id = order_items.order_id join items on items.item_id = order_items.item_id join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join qty_types on qty_types.qty_id = orders.qty_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                return ["saleItems" => $saleItems, "totals" => $totals];
                break;

            case '3':
                $sales = DB::select("SELECT orders.order_date, customers.cus_name, orders.order_no, users.name, payment_types.payment_desc, orders.order_total_amount as amount, sum(order_items.quantity * order_items.cost_price) as 'cost', sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_margin' from orders join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join payment_types on payment_types.payment_id = orders.payment_id join order_items on order_items.order_id = orders.order_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY orders.order_no ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                return ["sales" => $sales, "totals" => $totals];
                break;

            case '4':
                $salesItemsByUnitPrice = DB::select("SELECT items.item_name, qty_types.qty_desc, order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as 'sale_amount' from order_items join items on items.item_id = order_items.item_id join orders on orders.order_id = order_items.order_id join qty_types on qty_types.qty_id = orders.qty_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                return ["salesItemsByUnitPrice" => $salesItemsByUnitPrice, "totals" => $totals];
                break;

            case '5':
                $dailySalesSummary = DB::select("SELECT date(order_items.created_at) as 'order_date', sum(order_items.quantity) as volume, sum(order_items.amount) as amount, sum(order_items.quantity * order_items.cost_price) as cost, sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_profit' from order_items join orders on orders.order_id = order_items.order_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY orders.order_date ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                return ["dailySalesSummary" => $dailySalesSummary, "totals" => $totals];
                break;

            case '6':
                $saleItemsSummary = DB::select("SELECT orders.order_date, items.item_name, qty_types.qty_desc, order_items.quantity as volume, (order_items.price * order_items.quantity) as amount from order_items join items on items.item_id = order_items.item_id join orders on orders.order_id = order_items.order_id join qty_types on qty_types.qty_id = orders.qty_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                return ["saleItemsSummary" => $saleItemsSummary, "totals" => $totals];
                break;

            case '7':
                $allItemsTotalQty = 0;
                $allItems = DB::select("SELECT items.item_name, qty_types.qty_desc, item_qties.quantity as 'qty_in_store', item_prices.price as 'cost_price', item_prices.max_price as 'sale_price' from items join item_qties on (items.item_id = item_qties.item_id AND item_qties.qty_id = ?) join qty_types on qty_types.qty_id = ? join item_prices on (items.item_id = item_prices.item_id AND item_prices.qty_id = ?) where items.merchant_id = ? AND item_prices.store_id = ? AND item_qties.store_id = ? ", [$qtyId, $qtyId, $qtyId, Auth::user()->merchant_id, Auth::user()->store_id, Auth::user()->store_id]);
                (new Collection($allItems))->each(function ($each) use (&$allItemsTotalQty) {
                    $allItemsTotalQty += $each->qty_in_store;
                });
                return ["allItems" => $allItems, "totalQty" => $allItemsTotalQty];
                break;

            case '8':
                $totalQtyCancled = $totalAmountCancled = 0;
                $cancledItems = DB::select("SELECT orders.order_date, orders.order_no, customers.cus_name, users.name, items.item_name, qty_types.qty_desc,  order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as amount, orders.cancled_note as note FROM orders join order_items on orders.order_id = order_items.order_id join items on items.item_id = order_items.item_id join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join qty_types on qty_types.qty_id = orders.qty_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status ='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                (new Collection($cancledItems))->each(function ($each) use (&$totalQtyCancled, &$totalAmountCancled) {
                    $totalQtyCancled += $each->quantity;
                    $totalAmountCancled += $each->amount;
                });
                return ["cancledItems" => $cancledItems, "totalQty" => $totalQtyCancled, "totalAmount" => $totalAmountCancled];
                break;

            case '9':
                $totalQtyPurchased = $totalAmountPurchased = 0;
                $purchasedItems = DB::select("SELECT purchase_orders.purchase_date, suppliers.sup_company_name, payment_types.payment_desc, items.item_name, purchase_order_items.purchase_price, purchase_order_items.purchase_qty as quantity,  (purchase_order_items.purchase_price * purchase_order_items.purchase_qty) as amount  FROM purchase_orders join purchase_order_items on purchase_orders.purchase_id = purchase_order_items.purchase_id join items on items.item_id = purchase_order_items.item_id  join suppliers on suppliers.sup_id = purchase_orders.supplier_id join payment_types on payment_types.payment_id = purchase_orders.payment_id  where purchase_orders.store_id = ? AND (purchase_orders.purchase_date between ? AND ?) AND purchase_orders.cancled_date IS NULL AND purchase_orders.qty_id = ? ORDER BY purchase_orders.purchase_date", [Auth::user()->store_id, $req->fromDate, $req->toDate, $qtyId]);
                (new Collection($purchasedItems))->each(function ($each) use (&$totalQtyPurchased, &$totalAmountPurchased) {
                    $totalQtyPurchased += $each->quantity;
                    $totalAmountPurchased += $each->amount;
                });
                return ["purchasedItems" => $purchasedItems, "totalQty" => $totalQtyPurchased, "totalAmount" => $totalAmountPurchased];
                break;

            case '10':

                $result = array();

                $grandTotalQty = $grandTotalAmount = $grandTotalCost = $grandTotalGrossProfit = 0;

                $stores = DB::select("SELECT * FROM merchant_stores where merchant_id = ? ", [Auth::user()->merchant_id]);

                (new Collection($stores))->each(function ($eachStore) use ($req, $qtyId, &$result, &$grandTotalQty, &$grandTotalAmount, &$grandTotalCost, &$grandTotalGrossProfit) {
                    $totalQty = $totalAmount = $totalCost = $totalGrossProfit = 0;

                    $eachStoreSummary = DB::select("SELECT date(order_items.created_at) as 'order_date', sum(order_items.quantity) as volume, sum(order_items.amount) as amount, sum(order_items.quantity * order_items.cost_price) as cost, sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_profit' from order_items join orders on orders.order_id = order_items.order_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY orders.order_date ORDER BY orders.order_date", [$eachStore->store_id, $req->fromDate, $req->toDate, $qtyId]);

                    (new Collection($eachStoreSummary))->each(function ($each) use (&$totalQty, &$totalAmount, &$totalCost, &$totalGrossProfit) {
                        $totalQty += $each->volume;
                        $totalAmount += $each->amount;
                        $totalCost += $each->cost;
                        $totalGrossProfit += $each->gross_profit;
                    });
                    $grandTotalQty += $totalQty;
                    $grandTotalAmount += $totalAmount;
                    $grandTotalCost += $totalCost;
                    $grandTotalGrossProfit += $totalGrossProfit;

                    $result['stores'][] = ["store" => $eachStore->title, "data" => $eachStoreSummary, "total" => ["totalQty" => $totalQty, "totalAmount" => $totalAmount, "totalCost" => $totalCost, "totalGrossProfit" => $totalGrossProfit]];
                });

                $result['grandTotal'] = ["grandTotalQty" => $grandTotalQty, "grandTotalAmount" => $grandTotalAmount, "grandTotalCost" => $grandTotalCost, "grandTotalGrossProfit" => $grandTotalGrossProfit];
                return $result;
                break;

            case '11':
                $totalTicket = $totalQty = $totalAmount = 0;
                $cusByTicketList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, count(orders.cus_id) as tickets, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$req->fromDate, $req->toDate])->orderBy('tickets', 'DESC')->groupBy('orders.cus_id')->get();
                $cusByTicketList->each(function ($each) use (&$totalTicket, &$totalQty, &$totalAmount) {
                    $totalTicket += $each->tickets;
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });
                return ["cusByTicketList" => $cusByTicketList, "totalTicket" => $totalTicket, "totalQty" => $totalQty, "totalAmount" => $totalAmount];
                break;

            case '12':
                $totalQty = $totalAmount = 0;
                $cusByAmountList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$req->fromDate, $req->toDate])->orderBy('amount', 'DESC')->groupBy('customers.cus_name')->get();
                $cusByAmountList->each(function ($each) use (&$totalQty, &$totalAmount) {
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });
                return ["cusByAmountList" => $cusByAmountList, "totalQty" => $totalQty, "totalAmount" => $totalAmount];
                break;

            case '13':
                $totalQty = $totalAmount = 0;
                $cusByVolumeList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$req->fromDate, $req->toDate])->orderBy('qty', 'DESC')->groupBy('customers.cus_name')->get();
                $cusByVolumeList->each(function ($each) use (&$totalQty, &$totalAmount) {
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });
                return ["cusByVolumeList" => $cusByVolumeList, "totalQty" => $totalQty, "totalAmount" => $totalAmount];
                break;

            default:
                # code...
                break;
        }
    }

    public function downloadReport($fromDate, $toDate, $reportType, $qtyId)
    {

        if (($reportType >= 1) && ($reportType <= 6)) {

            $totalQty = $totalAmount = $totalCost = $totalGrossProfit = 0;
            $totalsQuery = DB::select("SELECT order_items.quantity, (order_items.price * order_items.quantity) as amount, (order_items.cost_price * order_items.quantity) as cost, ((order_items.price * order_items.quantity) - (order_items.cost_price * order_items.quantity)) as gross_profit  FROM orders join order_items on orders.order_id = order_items.order_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [Auth::user()->store_id, $fromDate, $toDate, $qtyId]);
            (new Collection($totalsQuery))->each(function ($each) use (&$totalQty, &$totalAmount, &$totalCost, &$totalGrossProfit) {
                $totalQty += $each->quantity;
                $totalAmount += $each->amount;
                $totalCost += $each->cost;
                $totalGrossProfit += $each->gross_profit;
            });
            $totals = ["totalQty" => $totalQty, "totalAmount" => $totalAmount, "totalCost" => $totalCost, "totalGrossProfit" => $totalGrossProfit];
        }

        switch ($reportType) {
            case '1':
                return Excel::download(new TopSalesStock(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Top_Sales_Stock_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '2':
                return Excel::download(new SaleItems(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Sale_Items_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '3':
                return Excel::download(new Sales(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Sales_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '4':
                return Excel::download(new SalesItemsByUnitPrice(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Sale_Items_Summary_Group_By_Unit_Price_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '5':
                return Excel::download(new DailySalesSummary(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Daily_Sales_Summary_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '6':
                return Excel::download(new SaleItemsSummary(["storeId" => Auth::user()->store_id, "fromDate" => $fromDate, "toDate" => $toDate, "totals" => $totals, "qtyId" => $qtyId]), "Sale_Items_Summary_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '7':
                $allItemsTotalQty = 0;
                $allItems = DB::select("SELECT items.item_name, qty_types.qty_desc, item_qties.quantity as 'qty_in_store', item_prices.price as 'cost_price', item_prices.max_price as 'sale_price' from items join item_qties on (items.item_id = item_qties.item_id AND item_qties.qty_id = ? ) join qty_types on qty_types.qty_id = ? join item_prices on (items.item_id = item_prices.item_id AND  item_prices.qty_id = ? ) where items.merchant_id = ? AND item_prices.store_id = ? AND item_qties.store_id = ?", [$qtyId, $qtyId, $qtyId, Auth::user()->merchant_id, Auth::user()->store_id, Auth::user()->store_id]);
                (new Collection($allItems))->each(function ($each) use (&$allItemsTotalQty) {
                    $allItemsTotalQty += $each->qty_in_store;
                });
                $totals = ["totalQty" => $allItemsTotalQty, "allItems" => $allItems];
                return Excel::download(new AllItems($totals), "All_Items.xlsx");
                break;

            case '8':
                $totalQtyCancled = $totalAmountCancled = 0;
                $cancledItems = DB::select("SELECT orders.order_date, orders.order_no, customers.cus_name, users.name, items.item_name, qty_types.qty_desc,  order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as amount, orders.cancled_note FROM orders join order_items on orders.order_id = order_items.order_id join items on items.item_id = order_items.item_id join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join qty_types on qty_types.qty_id = orders.qty_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status ='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date",   [Auth::user()->store_id, $fromDate, $toDate, $qtyId]);
                (new Collection($cancledItems))->each(function ($each) use (&$totalQtyCancled, &$totalAmountCancled) {
                    $totalQtyCancled += $each->quantity;
                    $totalAmountCancled += $each->amount;
                });
                $totals = ["totalQty" => $totalQtyCancled, "totalAmount" => $totalAmountCancled];
                return Excel::download(new CancledItems(["cancledItems" => $cancledItems, "totals" => $totals]), "Cancled_Items_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '9':
                $totalQtyPurchased = $totalAmountPurchased = 0;
                $purchasedItems = DB::select("SELECT purchase_orders.purchase_date, suppliers.sup_company_name, payment_types.payment_desc, items.item_name, purchase_order_items.purchase_price, purchase_order_items.purchase_qty as quantity,  (purchase_order_items.purchase_price * purchase_order_items.purchase_qty) as amount  FROM purchase_orders join purchase_order_items on purchase_orders.purchase_id = purchase_order_items.purchase_id join items on items.item_id = purchase_order_items.item_id  join suppliers on suppliers.sup_id = purchase_orders.supplier_id join payment_types on payment_types.payment_id = purchase_orders.payment_id  where purchase_orders.store_id = ? AND (purchase_orders.purchase_date between ? AND ?) AND purchase_orders.cancled_date IS NULL AND purchase_orders.qty_id = ? ORDER BY purchase_orders.purchase_date", [Auth::user()->store_id, $fromDate, $toDate, $qtyId]);
                (new Collection($purchasedItems))->each(function ($each) use (&$totalQtyPurchased, &$totalAmountPurchased) {
                    $totalQtyPurchased += $each->quantity;
                    $totalAmountPurchased += $each->amount;
                });
                $totals = ["totalQty" => $totalQtyPurchased, "totalAmount" => $totalAmountPurchased];
                return Excel::download(new PurchasedItems(["purchasedItems" => $purchasedItems, "totals" => $totals]), "Purchased_Items_" . $fromDate . "_TO_" . $toDate . ".xlsx");
                break;

            case '10':
                $result = collect([]);

                $grandTotalQty = $grandTotalAmount = $grandTotalCost = $grandTotalGrossProfit = 0;

                $stores = DB::select("SELECT * FROM merchant_stores where merchant_id = ? ", [Auth::user()->merchant_id]);

                (new Collection($stores))->each(function ($eachStore, $index) use ($stores, $fromDate, $toDate, $qtyId, &$result, &$grandTotalQty, &$grandTotalAmount, &$grandTotalCost, &$grandTotalGrossProfit) {

                    $totalQty = $totalAmount = $totalCost = $totalGrossProfit = 0;

                    $result = $result->merge([[strtoupper($eachStore->title)]]);  // Store Title

                    $result = $result->merge([['order_date' => "ORDER DATE", 'volume' => "QUANTITY", 'amount' => "AMOUNT", 'cost' => "COST", 'gross_profit' => "GROSS PROFIT"]]);

                    $eachStoreSummary = DB::select("SELECT date(order_items.created_at) as 'order_date', sum(order_items.quantity) as volume, sum(order_items.amount) as amount, sum(order_items.quantity * order_items.cost_price) as cost, sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_profit' from order_items join orders on orders.order_id = order_items.order_id where orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ?  GROUP BY orders.order_date ORDER BY orders.order_date", [$eachStore->store_id, $fromDate, $toDate, $qtyId]);

                    $result = $result->merge($eachStoreSummary);

                    (new Collection($eachStoreSummary))->each(function ($each) use (&$totalQty, &$totalAmount, &$totalCost, &$totalGrossProfit) {
                        $totalQty += $each->volume;
                        $totalAmount += $each->amount;
                        $totalCost += $each->cost;
                        $totalGrossProfit += $each->gross_profit;
                    });

                    $result = $result->merge([[]]); // create a line on the spreadsheet before total row
                    $result = $result->merge([['title' => "TOTAL", 'volume' => $totalQty, 'amount' => $totalAmount, 'cost' => $totalCost, 'gross_profit' => $totalGrossProfit]]);
                    $result = ($index != count($stores) - 1) ? $result->merge([[], []]) : $result->merge([[]]);  // Create 2 or 1 lines on the spreadsheet after each store

                    $grandTotalQty += $totalQty;
                    $grandTotalAmount += $totalAmount;
                    $grandTotalCost += $totalCost;
                    $grandTotalGrossProfit += $totalGrossProfit;
                });

                $result = $result->merge([["title" => "GRAND TOTAL", "grandTotalQty" => $grandTotalQty, "grandTotalAmount" => $grandTotalAmount, "grandTotalCost" => $grandTotalCost, "grandTotalGrossProfit" => $grandTotalGrossProfit]]);

                return Excel::download(new TotalSummary(["data" => $result]), "Total_Summary_" . $fromDate . "_TO_" . $toDate . ".xlsx");

                break;

            case '11':
                $totalTicket = $totalQty = $totalAmount = 0;

                $cusByTicketList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('count(orders.cus_id) as tickets, sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$fromDate, $toDate])->orderBy('tickets', 'DESC')->groupBy('orders.cus_id')->get();

                $cusByTicketList->each(function ($each) use (&$totalTicket, &$totalQty, &$totalAmount) {
                    $totalTicket += $each->tickets;
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });

                $totals = ["totalTicket" => $totalTicket, "totalQty" => $totalQty, "totalAmount" => $totalAmount];

                return Excel::download(new CustomerTicket(["cusByTicketList" => $cusByTicketList, "totals" => $totals]), "Top_Customers_By_Ticket_" . $fromDate . "_TO_" . $toDate . ".xlsx");

                break;

            case '12':
                $totalQty = $totalAmount = 0;

                $cusByAmountList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$fromDate, $toDate])->orderBy('amount', 'DESC')->groupBy('customers.cus_name')->get();

                $cusByAmountList->each(function ($each) use (&$totalQty, &$totalAmount) {
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });

                $totals = ["totalQty" => $totalQty, "totalAmount" => $totalAmount];

                return Excel::download(new CustomerAmount(["cusByAmountList" => $cusByAmountList, "totals" => $totals]), "Top_Customers_By_Amount_" . $fromDate . "_TO_" . $toDate . ".xlsx");

                break;

            case '13':
                $totalQty = $totalAmount = 0;

                $cusByVolumeList = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where(['orders.qty_id' => $qtyId])->whereBetween(DB::raw('date(orders.created_at)'), [$fromDate, $toDate])->orderBy('qty', 'DESC')->groupBy('customers.cus_name')->get();

                $cusByVolumeList->each(function ($each) use (&$totalQty, &$totalAmount) {
                    $totalQty += $each->qty;
                    $totalAmount += $each->amount;
                });

                $totals = ["totalQty" => $totalQty, "totalAmount" => $totalAmount];

                return Excel::download(new CustomerVolume(["cusByVolumeList" => $cusByVolumeList, "totals" => $totals]), "Top_Customers_By_Volume_" . $fromDate . "_TO_" . $toDate . ".xlsx");

                break;

            default:
                # code...
                break;
        }
    }

    public function itemQtyHistory(Request $req)
    {

        $fromDate = $req->fromDate;

        $toDate = $req->toDate;

        $qtyId = $req->qtyId;

        $itemId = $req->itemId;

        $result = DB::select("SELECT users.name, item_qty_logs.old_qty, item_qty_logs.new_qty, (item_qty_logs.new_qty - item_qty_logs.old_qty) as 'diff', item_qty_logs.date, item_qty_logs.time from item_qty_logs join users on users.user_id = item_qty_logs.user_id where item_qty_logs.store_id = ? AND item_qty_logs.item_id = ? AND item_qty_logs.qty_id = ? AND (item_qty_logs.date between ? AND ?) ORDER BY item_qty_logs.id ASC", [Auth::user()->store_id, $itemId, $qtyId, $fromDate, $toDate]);

        return $result;
    }

    public function downloadItemQtyHistory(Request $req, $fromDate, $toDate, $qtyId, $itemId)
    {

        $reqData = ["fromDate" => $fromDate, "toDate" => $toDate, "qtyId" => $qtyId, "itemId" => $itemId];

        $itemRecord = Item::where('item_id', $itemId)->first();

        $result = $this->itemQtyHistory($req->duplicate($reqData));

        return Excel::download(new ItemQtyHistory(["data" => $result]), $itemRecord->item_name . "_Qty_History_" . $fromDate . "_TO_" . $toDate . ".xlsx");
    }
}
