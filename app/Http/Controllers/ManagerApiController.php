<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Hash;
use TheSeer\Tokenizer\Exception;
use App\Http\Controllers\EnumController as Enum;
use App\Models\Credit;
use App\Models\Credit_log;
use App\Models\Credit_order;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Discount_item;
use App\Models\Discount_paid_log;
use App\Models\Item;
use App\Models\Item_category;
use App\Models\Item_price;
use App\Models\Item_price_log;
use App\Models\Item_qty;
use App\Models\Item_qty_log;
use App\Models\Item_tiered_price;
use App\Models\Item_tiered_price_log;
use App\Models\Merchant_store;
use App\Models\Order;
use App\Models\Payment_type;
use App\Models\Purchase_order;
use App\Models\Purchase_order_item;
use App\Models\Qty_type;
use App\Models\Quantity_conversion;
use App\Models\Quantity_conversion_log;
use App\Models\Stock_movement;
use App\Models\Supplier;
use App\Models\Transaction_type;
use App\Models\Transfer_item;
use App\Models\Transfer_order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use GuzzleHttp\Utils;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Validator;

class ManagerApiController extends Controller implements HasMiddleware
{
    static $mgrCtrl;

    function __construct(ManagersController $mgrCtrl)
    {
        self::$mgrCtrl = $mgrCtrl;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['_index']),
        ];
    }

    public function _index($token)
    {
        if (base64_encode($token) == Enum::KEY) {
            try {
                return HomeController::init();
            } catch (\Exception $e) {
                return $e;
            }
        }
        return response()->json(false);
    }

    static function userId()
    {
        return  Auth::user()->user_id;
    }

    function user()
    {
        $user =  response()->json(request()->user()->load(['userRole', 'userStore']));
        return $user;
    }

    function dashboard($date)
    {
        $orderSum = Order::where('order_status', '<>', Enum::CANCLED)->where(['store_id' => Auth::user()->store_id])->where('order_date', $date)->sum('order_total_amount');
        $orderCount = Order::where('order_status', '<>', Enum::CANCLED)->where(['store_id' => Auth::user()->store_id])->where('order_date', $date)->count('order_id');
        $purchaseIds = Purchase_order::select("purchase_id")->where(['store_id' => Auth::user()->store_id])->where('purchase_date', $date)->get();
        $purchaseOrders = Purchase_order_item::whereIn("purchase_id", $purchaseIds)->where("cancled_date", null)->count('purchase_id');
        $transferOrder = Transfer_order::where('transfer_status', Enum::SUCCESS)->where(['store_id' => Auth::user()->store_id])->where('transfer_date', $date)->count('transfer_id');
        $stockValue = collect(DB::select('SELECT SUM(item_qties.quantity * item_prices.price) AS stockValue FROM item_qties JOIN item_prices ON item_qties.item_id = item_prices.item_id AND item_qties.qty_id = item_prices.qty_id WHERE item_prices.store_id = ? AND item_qties.store_id = ? ', [Auth::user()->store_id, Auth::user()->store_id]))->first()->stockValue ?? 0;
        $totalQuantity = Item_qty::join('items', function ($q) {
            $q->on('items.item_id', '=', 'item_qties.item_id')->on('item_qties.qty_id', '=', 'items.default_qty_id');
        })->select(DB::raw('SUM(quantity) as totalQty'))->where(['item_qties.store_id' => Auth::user()->store_id])->first()->totalQty ?? 0;
        return json_encode(["salesAmount" => (string)$orderSum, "salesCount" => $orderCount, "purchaseCount" => $purchaseOrders, "transferCount" => $transferOrder, "stockValue" => $stockValue, 'totalQuantity' => $totalQuantity, 'storeId' => Auth::user()->store_id]);
    }

    function getItemCategory()
    {
        return response()->json(Item_category::orderBy('cat_desc', "ASC")->get());
    }

    function inventory()
    {
        $items =  Item::with('default_unit')->join('item_qties', function ($q) {
            $q->on('items.item_id', '=', 'item_qties.item_id')->on('item_qties.qty_id', '=', 'items.default_qty_id');
        })->join('item_prices', function ($q) {
            $q->on('items.item_id', '=', 'item_prices.item_id')->on('item_prices.qty_id', '=', 'items.default_qty_id');
        })->where(['item_qties.store_id' => Auth::user()->store_id, 'item_prices.store_id' => Auth::user()->store_id])->get();
        $category = item_category::orderBy('cat_desc', "ASC")->get();
        return response()->json(['inv' => $items, 'category' => $category]);
    }

    function selectedInv($itemId)
    {
        $res = item::with(['item_category', 'default_unit', 'conversion.srcQtyType', 'conversion.cnvQtyType', 'item_qty.qty_type', 'item_qty.itemPrice', 'item_qty.itemTieredPrice', 'item_qty' => function ($q) {
            $q->where(['store_id' => Auth::user()->store_id]);
        }])->where(['item_id' => $itemId])->first();
        return $res;
    }

    public function updateSelectedInventory(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            item::where('item_id', $data['itemId'])->where(['store_id' => Auth::user()->store_id])->update(['item_name' => $data['itemName'], 'category_id' => $data['itemCategory'], 'min_stock_level' => $data['minStockLevel'], 'manufacturer' => $data['manufacturer'], 'reorder_level' => $data['reorderLevel'], 'updated_at' => Carbon::now()]);
            foreach ($data['qtyPrice'] as $key => $value) {
                $initialQty = item_qty::where('item_id', $data['itemId'])->where('qty_id', $value['qtyId'])->where('store_id', Auth::user()->store_id)->first();
                item_qty::where('item_id', $data['itemId'])->where('qty_id', $value['qtyId'])->where('store_id', Auth::user()->store_id)->update(['quantity' => $value['qty'], 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(['user_id' => self::userId(), 'store_id' => Auth::user()->store_id, 'qty_id' => $value['qtyId'], 'item_id' => $data['itemId'], 'old_qty' => $initialQty->quantity, 'new_qty' => $value['qty'], 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                $itemPrice = Item_price::where(['item_id' => $data['itemId'], "qty_id" => $value['qtyId']])->where('store_id', Auth::user()->store_id)->first();
                item_price::where(['item_id' => $data['itemId'], "qty_id" => $value['qtyId']])->where('store_id', Auth::user()->store_id)->update(['price' => $value['costPrice'], 'min_price' => $value['costToSell'], 'max_price' => $value['salePrice'], 'updated_at' => Carbon::now()]);
                Item_price_log::insert(['user_id' => self::userId(), 'store_id' => Auth::user()->store_id, 'item_id' => $data['itemId'], 'qty_id' => $value['qtyId'], 'old_price' => $itemPrice->price, 'old_min_price' => $itemPrice->min_price, 'old_max_price' => $itemPrice->max_price, 'new_price' => $value['costPrice'], 'new_min_price' => $value['costToSell'], 'new_max_price' => $value['salePrice'], 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["response" => "Item Update Failed", "status" => false]);
        }
        DB::commit();
        return response()->json(["response" => "Item Details Updated", "status" => true]);
    }

    public function updateStockTransferUnit(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {

            $qtyConversion = Quantity_conversion::updateOrCreate(['item_id' => $data['itemId'], 'store_id' => Auth::user()->store_id], ['merchant_id' => Auth::user()->merchant_id, 'initial_qty_id' => $data['fromQtyId'], 'converted_qty_id' => $data['toQtyId'], 'initial_qty' => $data['initialQty'], 'converted_qty' => $data['convertedQty'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            Quantity_conversion_log::create(['conversion_id' => $qtyConversion->conversion_id, 'user_id' => Auth::user()->user_id, 'item_id' => $data['itemId'], 'store_id' => Auth::user()->store_id, 'initial_qty_id' => $data['fromQtyId'], 'converted_qty_id' => $data['toQtyId'], 'initial_qty' => $data['initialQty'], 'converted_qty' => $data['convertedQty'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["response" => "Transfer Unit Update Failed", "status" => false]);
        }
        DB::commit();
        return response()->json(["response" => "Transfer Unit Updated", "status" => true, "auth" => Auth::user()]);
    }

    function updatePrice(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            foreach ($data['prices'] as $key => $value) {
                $itemPrice = item_price::where(['item_id' => $data['itemId'], "qty_id" => $value['qtyId']])->where('store_id', Auth::user()->store_id)->first();
                item_price::where(['item_id' => $data['itemId'], "qty_id" => $value['qtyId']])->where('store_id', Auth::user()->store_id)->update(['price' => $value['costPrice'], 'min_price' => $value['costToSell'], 'max_price' => $value['salePrice'], 'walkin_price' => $value['walkinPrice'], 'updated_at' => Carbon::now()]);
                item_price_log::insert(['user_id' => self::userId(), 'store_id' => Auth::user()->store_id, 'item_id' => $data['itemId'], 'qty_id' => $value['qtyId'], 'old_price' => $itemPrice->price, 'old_min_price' => $itemPrice->min_price, 'old_max_price' => $itemPrice->max_price, 'new_price' => $value['costPrice'], 'new_min_price' => $value['costToSell'], 'new_max_price' => $value['salePrice'], 'old_walkin_price' => $itemPrice->walkin_price, 'new_walkin_price' => $value['walkinPrice'], 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
            foreach ($data['tieredData'] as $val) {
                $tieredModel = Item_tiered_price::where(["id" => $val['id']]);
                if ($tieredModel->count() != 0) {
                    $first = $tieredModel->first();
                    Item_tiered_price_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'tiered_price_id' => $first->id, 'qty_id' => $val['qtyId'], 'item_id' => $data['itemId'], 'old_qty' => $first->qty, 'old_price' => $first->price, 'new_qty' => $val['qty'], 'new_price' => $val['price'], 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                } else {
                    Item_tiered_price::where(['merchant_id' => Auth::user()->merchant_id,'store_id' => Auth::user()->store_id, 'qty_id' => $val['qtyId'], 'item_id' => $data['itemId']])->delete();
                    $tieredPrices[] = ['merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $val['qtyId'], 'item_id' => $data['itemId'], 'qty' => $val['qty'], 'price' => $val['price'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                }
            }
            item_tiered_price::insert($tieredPrices);
            
        } catch (\Exception $e) {
            Log::error("price update failed", ["reason" => $e, "request" => $data]);
            DB::rollback();
            return response()->json(["response" => "Price Update Failed", "status" => false]);
        }
        DB::commit();
        return response()->json(["response" => "Price Updated", "status" => true]);
    }


    function addItem(Request $req)
    {
        try {
            self::$mgrCtrl->addItem($req);
        } catch (\Exception $e) {
            return json_encode($e);
            return response()->json(["response" => "Item Insertion Failed", "status" => false]);
        }
        return response()->json(["response" => "Item Inserted", "status" => true]);
    }

    function getSuppliers()
    {
        $suppliers = Supplier::where(['merchant_id' => Auth::user()->merchant_id])->orderBy('sup_id', "DESC")->get();
        return response()->json($suppliers);
    }

    function addSupplier(Request $req)
    {
        $data = $req->json()->all();
        try {
            $supplier = new Supplier();
            $supplier->sup_company_name = $data['companyName'];
            $supplier->sup_contact_name = $data['contactName'];
            $supplier->sup_mobile = $data['mobileNumber'];
            $supplier->sup_mail = $data['mail'];
            $supplier->sup_address = $data['address'];
            $supplier->sup_website = $data['website'];
            $supplier->merchant_id = Auth::user()->merchant_id;
            $supplier->store_id =  Auth::user()->store_id;
            $supplier->save();
        } catch (\Exception $e) {
            return response()->json(["response" => "Registration Failed", "status" => false]);
        }
        return response()->json(["response" => "Supplier Saved", "status" => true]);
    }

    function updateSupplier(Request $req)
    {
        $data =  $req->json()->all();
        try {
            Supplier::where('sup_id', $data['supId'])->update(['sup_contact_name' => $data['contactName'], 'sup_company_name' => $data['companyName'], 'sup_mobile' => $data['mobileNumber'], 'sup_mail' => $data['mail'], 'sup_address' => $data['address'], 'sup_website' => $data['website'], 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            return response()->json(['response' => "Update Failed", 'status' => false]);
        }
        return response()->json(['response' => "Supplier Updated", 'status' => true]);
    }

    function getPurchaseData()
    {
        $suppliers = Supplier::where('merchant_id', Auth::user()->merchant_id)->orderBy('sup_company_name', 'ASC')->get();
        $paymentTypes = Payment_type::orderBy('payment_desc', 'ASC')->get();
        $qtyTypes = Qty_type::orderBy('qty_desc', 'DESC')->get();
        $items = Item::where('merchant_id', Auth::user()->merchant_id)->get();
        $datas = ['supplier' => $suppliers, 'payment' => $paymentTypes, 'qtyTypes' => $qtyTypes, 'items' => $items];
        return response()->json($datas);
    }

    function savePurchase(Request $req)
    {

        $data = Utils::jsonDecode($req['data']);
        $list = Utils::jsonDecode($req['list']);
        DB::beginTransaction();
        try {
            $purchaseOrder = array(
                "qty_id" => $data->qty->qty_id,
                "merchant_id" => Auth::user()->merchant_id,
                "store_id" => Auth::user()->store_id,
                "supplier_id" => $data->supplier->sup_id,
                "payment_id" => $data->payment->payment_id,
                "user_id" => self::userId(),
                "purchase_time" => $req['time'],
                "purchase_date" => date("Y/m/d"),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            );
            $dbPurchaseOrder = DB::table('purchase_orders')->insertGetId($purchaseOrder, 'purchase_id');
            foreach ($list as $key => $value) {
                $purchaseOrderItems[] = array(
                    "purchase_id" => $dbPurchaseOrder,
                    "item_id" => $value->item->item_id,
                    "purchase_qty" => $value->quantity,
                    "purchase_price" => $value->price,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                );
            }
            Purchase_order_item::insert($purchaseOrderItems);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["response" => "Purchase Failed", "status" => false]);
        }
        DB::commit();
        return response()->json(["response" => "Purchase Saved", "status" => true]);
    }

    public function getPendingPurchases()
    {
        $purchaseIds = Purchase_order::select("purchase_id")->where(['store_id' => Auth::user()->store_id])->get();
        $pendingPurchases = Purchase_order_item::whereIn("purchase_id", $purchaseIds)->where(function ($query) {
            $query->where('created_at', 'like', date('Y-m-d') . '%')->where('purchase_status', '<>', 'CANCLED')->orWhere("purchase_status", "PENDING");
        })->with(['items', 'Purchase_order.Supplier'])->orderBy('purchase_item_id', 'DESC')->get();
        return $pendingPurchases->toArray();
    }

    public function confirmDelivery(Request $req)
    {
        $data = collect($req->json()->all());
        DB::beginTransaction();
        try {
            $tranType = Transaction_type::where("trans_desc", "PURCHASE")->first()->trans_id;
            $data->each(function ($item, $key) use ($tranType) {
                Purchase_order_item::where("purchase_item_id", $item['purchaseItemId'])->update(["confirm_user_id" => Auth::user()->user_id, "purchase_status" => Enum::SUCCESS, "confirm_date" => date("Y/m/d"), "confirm_time" => date('h:i:s')]);
                $itemQty = Item_qty::where([["item_id", '=', $item['itemId']], ["qty_id", '=', $item['qtyId']]])->where(['store_id' => Auth::user()->store_id])->first();
                $newQty =  ($item['quantity']) + ($itemQty->quantity);
                Item_qty::where([["item_id", '=', $item['itemId']], ["qty_id", '=', $item['qtyId']]])->where(['store_id' => Auth::user()->store_id])->update(["quantity" => $newQty]);
                Item_qty_log::insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $item['qtyId'], "item_id" => $item['itemId'], "old_qty" => $itemQty->quantity, "new_qty" => $newQty, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => date('h:i:s'), "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);

                $itemPrice = Item_price::where(['item_id' => $item['itemId'], "qty_id" => $item['qtyId']])->where(['store_id' => Auth::user()->store_id])->first();
                Item_price::where(['item_id' => $item['itemId'], "qty_id" => $item['qtyId']])->where(['store_id' => Auth::user()->store_id])->update(['price' => $item['purchasePrice'], 'min_price' => $item['purchasePrice'],  'updated_at' => Carbon::now()]);
                Item_price_log::insert(['user_id' => self::userId(), "store_id" => Auth::user()->store_id, 'item_id' => $item['itemId'], 'qty_id' => $item['qtyId'], 'old_price' => $itemPrice->price, 'old_min_price' => $itemPrice->min_price, 'old_max_price' => $itemPrice->max_price, 'new_price' => $item['purchasePrice'], 'new_min_price' => $item['purchasePrice'], 'new_max_price' => $itemPrice->max_price, 'date' => date('Y-m-d'), 'time' => date('h:i:s'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            });
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Delivery Confirmed", 'status' => true]);
    }

    public function transferStock(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $conversion = Quantity_conversion::where('conversion_id', $data['conversionId'])->first();
            if ($conversion == null)   return response()->json(['response' => "Unit Formula Invalid", 'status' => false]);
            $fromQty = Item_qty::where(['item_id' => $data['itemId'], 'qty_id' => $data['fromQtyId']])->where('store_id', Auth::user()->store_id)->first();
            if (($data['transferQty'] <= 0) || ($data['transferQty'] > $fromQty['quantity']))   return response()->json(['response' => "Stock Quantity Exceeded", 'status' => false]);
            $transactionType = Transaction_type::where('trans_desc', 'TRANSFER')->first();
            $transferId = DB::table('transfer_orders')->insertGetid(['user_id_transfer' => Auth::user()->user_id, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'transfer_status' => Enum::SUCCESS, 'user_id_confirmed' => Auth::user()->user_id, 'confirmed_date' => date('Y-m-d'), 'confirmed_time' => $data['time'], 'transfer_date' => date('Y/m/d'), 'transfer_time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'transfer_id');


            $transferredQty = ($data['transferQty'] * $conversion['converted_qty']) / ($conversion['initial_qty']);
            Transfer_item::insert(['transfer_id' => $transferId, 'transfer_status' => Enum::SUCCESS, 'user_id_confirmed' => Auth::user()->user_id, 'confirmed_date' => date('Y-m-d'), 'confirmed_time' => $data['time'], 'item_id' => $data['itemId'], 'transfer_qty' => $data['transferQty'], 'transferred_qty' => $transferredQty, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            $itemQtySrc = Item_qty::where([['item_id', '=', $data['itemId']], ['qty_id', '=', $data['fromQtyId']]])->where('store_id', Auth::user()->store_id)->first();
            $oldSrcQty = $itemQtySrc->quantity;
            $newSrcQty = $oldSrcQty - ($data['transferQty']);
            $itemQtySrc->quantity = $newSrcQty;
            $itemQtySrc->updated_at = Carbon::now();
            $itemQtySrc->save();
            Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $data['fromQtyId'], 'item_id' => $data['itemId'], 'old_qty' => $oldSrcQty, 'new_qty' => $newSrcQty, 'trans_id' => $transactionType->trans_id, 'date' => date('Y/m/d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            $itemQtyTrn = Item_qty::where([['item_id', '=', $data['itemId']], ['qty_id', '=', $data['toQtyId']]])->where('store_id', Auth::user()->store_id)->first();
            $oldQty = $itemQtyTrn->quantity;
            $newQty = $oldQty + $transferredQty;
            $itemQtyTrn->quantity = $newQty;
            $itemQtyTrn->updated_at = Carbon::now();
            $itemQtyTrn->save();
            Item_qty_log::insert(['user_id' => Auth::user()->user_id, 'store_id' => Auth::user()->store_id, 'qty_id' => $data['toQtyId'], 'item_id' => $data['itemId'], 'old_qty' => $oldQty, 'new_qty' => $newQty, 'trans_id' => $transactionType->trans_id, 'date' => date('Y/m/d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Stock Transfer Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Stock Transferred", 'status' => true]);
    }

    function getCustomers()
    {
        $cus = self::$mgrCtrl->fetchCustomers();
        $qtyTypes = Qty_type::all();
        $items = Item::where("merchant_id", Auth::user()->merchant_id)->get();
        $paymentTypes = Payment_type::orderBy('payment_desc', 'ASC')->get();
        $creditTypes = [['credit_desc' => Enum::CREDIT], ['credit_desc' => Enum::DEBIT]];
        $res = ["cus" => $cus, "qtyTypes" => $qtyTypes, "items" => $items, 'payments' => $paymentTypes, 'creditTypes' => $creditTypes];
        return response()->json($res);
    }

    public function getEachcustomer($id)
    {
        $cus = Customer::where('cus_id', $id)->with(['credit', 'discount', 'creditOrders.order.qty', 'creditOrders' => function ($query) {
            $query->where('credit_order_status', Enum::OUTSTANDING);
        }, 'discountItems.item', 'discountItems.unit', 'payment'])->first();
        return $cus;
    }

    function addCustomer(Request $req)
    {
        $data = $req->json()->all();
        $validate = Validator::make(
            $data,
            [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ]
        );
        if ($validate->fails()) {
            return response()->json(['response' => "Fill Required fields", 'status' => false]);
        }

        DB::beginTransaction();
        try {
            $id = Customer::insertGetId(['registered_by_user_id' => Auth::user()->user_id, 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'cus_name' => $data['name'], 'cus_mobile' => $data['phone'], 'cus_mail' => $data['email'], 'cus_address' => $data['address'], 'payment_id' => $data['payment']['payment_id'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'cus_id');
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

    public function updatecustomer(Request $req)
    {
        $data = $req->json()->all();
        try {
            Customer::where('cus_id', $data['cus_id'])->update(['cus_name' => $data['name'], 'cus_mobile' => $data['phone'], 'cus_mail' => $data['email'], 'cus_address' => $data['address'], 'payment_id' => $data['payment']['payment_id'], 'cus_type' => $data['cusType'], 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            return $e;
            return response()->json(['response' => "Update Failed", 'status' => false]);
        }
        return response()->json(['response' => "Customer Updated", 'status' => true]);
    }

    public function creditProcess(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $credit = Credit::where('cus_id', $data['cusId'])->first();
            if ($data['creditType']['credit_desc'] == ENUM::CREDIT) {
                $new = $credit->available_credit + $data['creditAmount'];
                Credit::where('cus_id', $data['cusId'])->update(['available_credit' => $new, 'updated_at' => Carbon::now()]);
                Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::CREDIT, 'old_credit' => $credit->available_credit, 'new_credit' => $new, 'credit_date' => date("Y-m-d"), 'credit_time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
            if ($data['creditType']['credit_desc'] == ENUM::DEBIT) {
                $newdebit = $credit->available_credit - $data['creditAmount'];
                Credit::where('cus_id', $data['cusId'])->update(['available_credit' => $newdebit, 'updated_at' => Carbon::now()]);
                Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::DEBIT, 'old_credit' => $credit->available_credit, 'new_credit' => $newdebit, 'credit_date' => date("Y-m-d"), 'credit_time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Credit Updated", 'status' => true]);
    }

    public function payDebit(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $credit = Credit::where('cus_id', $data['cusId'])->first();
            $avail = $credit->available_credit + $data['amount'];
            $out = $credit->out_credit - $data['amount'];
            Credit::where('cus_id', $data['cusId'])->update(['available_credit' => $avail, 'out_credit' => $out, 'updated_at' => Carbon::now()]);
            Credit_order::where('cus_id', $data['cusId'])->where('credit_order_id', $data['creditOrderId'])->update(['credit_order_status' => Enum::PAID, 'date_paid' => date('Y-m-d'), 'time_paid' => $data['time'], 'updated_at' => Carbon::now()]);
            Credit_log::insert(['user_id' => Auth::user()->user_id, 'credit_order_id' => $data['creditOrderId'], 'credit_id' => $credit->credit_id, 'credit_log_status' => Enum::PAID, 'old_credit' => $credit->available_credit, 'new_credit' => $avail, 'credit_date' => date("Y-m-d"), 'credit_time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Debt Cleared", 'status' => true]);
    }

    public function payDiscount(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $cusDiscount = Discount::where('cus_id', $data['cusId'])->first();
            if (($data['amount'] < 1) || ($data['amount'] > $cusDiscount->discount_credit)) {
                return response()->json(['response' => 'Discount Balance Exceeded', 'status' => false]);
            }
            $newDis = $cusDiscount->discount_credit - $data['amount'];
            Discount::where('cus_id', $data['cusId'])->update(['discount_credit' => $newDis, 'updated_at' => Carbon::now()]);
            Discount_paid_log::insert(['cus_id' => $data['cusId'], 'user_id' => Auth::user()->user_id, 'paid_amount' => $data['amount'], 'date_paid' => date("Y-m-d"), 'time_paid' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['response' => 'Operation Failed', 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => 'Discount Paid', 'status' => true]);
    }

    public function addDiscount(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $existDiscount = Discount_item::where('item_id', $data['item']['item_id'])->where('qty_id', $data['qty']['qty_id'])->where('cus_id', $data['cusId'])->count();
            if ($existDiscount >= 1) {
                return response()->json(['response' => 'Discount Exist!', 'status' => false]);
            }
            Discount_item::insert(['cus_id' => $data['cusId'], 'user_id_enabled' => Auth::user()->user_id, 'item_id' => $data['item']['item_id'], 'qty_id' => $data['qty']['qty_id'], 'item_qty' => $data['quantity'], 'discount_amount' => $data['amount'], 'enabled_date' => date('Y-m-d'), 'enabled_time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['response' => 'Operation Failed', 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => 'Discount Added', 'status' => true]);
    }

    public function delDiscount(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            Discount_item::where('item_id', $data['itemId'])->where('qty_id', $data['qtyId'])->where('cus_id', $data['cusId'])->delete();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Discount Removed", 'status' => true]);
    }

    function getUsers()
    {
        $data = self::$mgrCtrl->fetchUsers();
        return $data;
    }

    function getRoles()
    {
        $data = self::$mgrCtrl->fetchRoles();
        return json_encode(["roles" => $data]);
    }

    function addUser(Request $req)
    {
        $data =  $req->json()->all();
        $validate = Validator::make(
            $data,
            [
                'name' => 'required',
                'username' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'password' => 'required',
            ]
        );
        if ($validate->fails()) {
            return response()->json(['response' => "Fill Required fields", 'status' => false]);
        }

        $status = ($data['status']['value'] == 'ACTIVE') ? Enum::ACTIVE : Enum::INACTIVE;
        $password = Hash::make($data['password']);
        try {
            user::insert(['name' => $data['name'], 'merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'email' => $data['email'], 'phone' => $data['phone'], 'username' => $data['username'], 'password' => $password, 'role' => $data['role']['role_id'], 'status' => $status, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062)
                return response()->json(['response' => "User Exits", 'status' => false]);
            else
                return response()->json(['response' => "User Registration Failed", 'status' => false]);
        }
        return response()->json(['response' => "User Registered", 'status' => true]);
    }

    function getUser($userId)
    {
        $res = User::where('user_id', $userId)->with('userRole')->first();
        return response()->json($res);
    }

    function userStatus($userId, $status)
    {
        $res = self::$mgrCtrl->userStatus($userId, $status);
        return $res;
    }

    function salesHistory($date)
    {
        return Order::with(['payment', 'qty', 'items.item', 'user', 'discount'])->join('customers', 'orders.cus_id', '=', 'customers.cus_id')->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->where('order_date', $date)->orderBy('order_id', 'DESC')->get();
    }

    function transferHistory($date)
    {
        $res = self::$mgrCtrl->transferHistory($date);
        return $res;
    }

    function purchaseHistory($date)
    {
        $res = self::$mgrCtrl->purchaseHistory($date);
        return $res;
    }

    public function cancleSalesOrder(Request $req)
    {
        $data = $req->json()->all();
        try {
            self::$mgrCtrl->cancleSalesOrder($data['orderId'], $data['time']);
            Order::where('order_id', $data['orderId'])->update(['cancled_note' => $data['comments']]);
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Order Cancled", 'status' => true]);
    }

    public function canclePurchase($purchaseId, $itemId, $time)
    {
        try {
            $res = self::$mgrCtrl->canclePurchase($purchaseId, $itemId, $time);
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Purchase Cancled", 'status' => true]);
    }

    public function cancleTransfer($transferId, $time)
    {
        try {
            $res = self::$mgrCtrl->cancleTransfer($transferId, $time);
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Purchase Cancled", 'status' => true]);
    }

    public function clearUserSession($userId)
    {
        try {
            User::where('user_id', $userId)->update(['user_agent' => null]);
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Session Cleared", 'status' => true]);
    }

    public function reprintReceipt($orderId)
    {
        try {
            Order::where('order_id', $orderId)->update(['receipt_printed' => 0]);
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Reprint Approved", 'status' => true]);
    }

    public function topSales(Request $req)
    {
        $data = $req->json()->all();
        $orderIds = Order::select('order_id')->where('order_status', '<>', Enum::CANCLED)->where(['store_id' => Auth::user()->store_id])->whereBetween(DB::raw('date(created_at)'), [$data['initialDate'], $data['finalDate']])->get();
        $orderIdLists = $orderIds->map(function ($each) {
            return $each->order_id;
        });
        $result = Item::join('order_items', 'order_items.item_id', '=', 'items.item_id')->select('items.item_name as name', DB::raw('sum(quantity) as qty, sum(amount) as amount'))->whereIn('order_items.order_id', $orderIdLists)->groupBy('items.item_name')->orderBy('qty', 'DESC')->get();
        return response()->json($result);
    }

    public function getTopQuantity()
    {
        $data = Item::join('item_qties', function ($q) {
            $q->on('items.item_id', '=', 'item_qties.item_id')->on('items.default_qty_id', '=', 'item_qties.qty_id');
        })->join('qty_types', 'items.default_qty_id', '=', 'qty_types.qty_id')->join('item_prices', function ($q) {
            $q->on('items.item_id', '=', 'item_prices.item_id')->on('items.default_qty_id', '=', 'item_prices.qty_id');
        })->where('item_qties.quantity', '>', '0')->where(['item_prices.store_id' => Auth::user()->store_id, 'item_qties.store_id' => Auth::user()->store_id])->orderBy('quantity', 'ASC')->get();
        return response()->json($data);
    }

    public function topCustomersVolume(Request $req)
    {
        $data = $req->json()->all();
        $result = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->whereBetween(DB::raw('date(orders.created_at)'), [$data['initialDate'], $data['finalDate']])->orderBy('qty', 'DESC')->groupBy('customers.cus_name')->get();
        return response()->json($result);
    }

    public function topCustomersAmount(Request $req)
    {
        $data = $req->json()->all();
        $result = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->whereBetween(DB::raw('date(orders.created_at)'), [$data['initialDate'], $data['finalDate']])->orderBy('amount', 'DESC')->groupBy('customers.cus_name')->get();
        return response()->json($result);
    }

    public function topCustomersTickets(Request $req)
    {
        $data = $req->json()->all();
        $result = Order::join('customers', 'customers.cus_id', '=', 'orders.cus_id')->select('customers.cus_name as name', DB::raw('sum(order_total_qty) as qty, count(orders.cus_id) as tickets, sum(order_total_amount) as amount'))->where('order_status', '<>', Enum::CANCLED)->where(['orders.store_id' => Auth::user()->store_id])->whereBetween(DB::raw('date(orders.created_at)'), [$data['initialDate'], $data['finalDate']])->orderBy('tickets', 'DESC')->groupBy('orders.cus_id')->get();
        return response()->json($result);
    }

    public function createStore(Request $req)
    {
        $data = $req->json()->all();
        $qtyList = array();
        $priceList = array();
        DB::beginTransaction();
        try {
            $store =  Merchant_store::create(["merchant_id" => Auth::user()->merchant_id, "title" => $data["title"], "address" => $data["address"], "telephone" => $data["mobile"], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $qtyTypes = Qty_type::all();
            $items = Item::where(['merchant_id' => Auth::user()->merchant_id])->get();
            foreach ($qtyTypes as $qtyType) {
                foreach ($items as $item) {
                    $qtyList[] = ['merchant_id' => Auth::user()->merchant_id, 'store_id' => $store->store_id, 'qty_id' => $qtyType->qty_id, 'item_id' => $item->item_id, 'quantity' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                    $priceList[] = ['merchant_id' => Auth::user()->merchant_id, 'store_id' => $store->store_id, 'qty_id' => $qtyType->qty_id, 'item_id' => $item->item_id, 'price' => 0, 'min_price' => 0, 'max_price' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                }
            }
            Item_qty::insert($qtyList);
            Item_price::insert($priceList);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Store Creation Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Store Created Successfully", 'status' => true]);
    }

    public function fetchStore()
    {
        $result = Merchant_store::where('merchant_id', Auth::user()->merchant_id)->get();
        return response()->json($result);
    }

    public function switchStore(Request $req)
    {
        $data = $req->json()->all();
        try {
            User::where('user_id', Auth::user()->user_id)->update(["store_id" => $data['store_id']]);
            $store = Merchant_store::where("store_id", $data['store_id'])->first();
        } catch (\Exception $e) {
            return response()->json(['response' => "Operation Failed", 'status' => false]);
        }
        return response()->json(['response' => "Store Switched", 'status' => true, 'store' => $store]);
    }

    public function stockMovementData()
    {
        $result = Merchant_store::where('merchant_id', Auth::user()->merchant_id)->where("store_id", '<>', Auth::user()->store_id)->get();
        return response()->json($result);
    }

    public function stockMovement(Request $req)
    {
        $data = $req->json()->all();
        DB::beginTransaction();
        try {
            $transId = Transaction_type::where("trans_desc", Enum::MOVEMENT)->first();

            $transferStore = Item_qty::where(["item_id" => $data['itemId'], "qty_id" => $data['qtyId'], "store_id" => Auth::user()->store_id])->first();

            if (!(($data['quantity'] > 0) && ($data['quantity'] <= $transferStore->quantity)))  return response()->json(['response' => "Stock Quantity Exceeded", 'status' => false]);

            $receiveStore = Item_qty::where(["item_id" => $data['itemId'], "qty_id" => $data['qtyId'], "store_id" => $data['receivingStoreId']])->first();

            $transferStorePrice = Item_price::where(["item_id" => $data['itemId'], "qty_id" => $data['qtyId'], "store_id" => Auth::user()->store_id])->first();

            $receiveStorePrice = Item_price::where(["item_id" => $data['itemId'], "qty_id" => $data['qtyId'], "store_id" => $data['receivingStoreId']])->first();

            $newTransferStoreQty = ($transferStore->quantity) - ($data['quantity']);

            $newReceiveStoreQty =  ($receiveStore->quantity) + ($data['quantity']);

            Item_qty_log::insert(['user_id' => self::userId(), 'store_id' => Auth::user()->store_id, 'qty_id' => $data['qtyId'], "trans_id" => $transId->trans_id, 'item_id' => $data['itemId'], 'old_qty' => $transferStore->quantity, 'new_qty' => $newTransferStoreQty, 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            Item_qty_log::insert(['user_id' => self::userId(), 'store_id' => $data['receivingStoreId'], 'qty_id' => $data['qtyId'], "trans_id" => $transId->trans_id, 'item_id' => $data['itemId'], 'old_qty' => $receiveStore->quantity, 'new_qty' => $newReceiveStoreQty, 'date' => date('Y-m-d'), 'time' => $data['time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            Item_price_log::insert([
                'user_id' => self::userId(),
                'store_id' => $data['receivingStoreId'],
                'item_id' => $data['itemId'],
                'qty_id' => $data['qtyId'],
                'old_price' => $receiveStorePrice->price,
                'old_min_price' => $receiveStorePrice->min_price,
                'old_max_price' => $receiveStorePrice->max_price,
                'new_price' => $transferStorePrice->price,
                'new_min_price' => $transferStorePrice->min_price,
                'new_max_price' => $transferStorePrice->max_price,
                'old_walkin_price' => $receiveStorePrice->walkin_price,
                'new_walkin_price' => $transferStorePrice->walkin_price,
                'date' => date('Y-m-d'),
                'time' => $data['time'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            Stock_movement::insert(["user_id" => Auth::user()->user_id, "merchant_id" => Auth::user()->merchant_id, "item_id" => $data['itemId'], "transferring_store_id" => Auth::user()->store_id, "receiving_store_id" => $data['receivingStoreId'], "qty_id" => $data['qtyId'], "quantity" => $data['quantity'], "transferring_store_old_quantity" => $transferStore->quantity, "transferring_store_new_quantity" => $newTransferStoreQty, "receiving_store_old_quantity" => $receiveStore->quantity, "receiving_store_new_quantity" => $newReceiveStoreQty, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            $transferStore->update(['quantity' => $newTransferStoreQty]);

            $receiveStore->update(['quantity' => $newReceiveStoreQty]);

            $receiveStorePrice->update(['price' => $transferStorePrice->price, 'min_price' => $transferStorePrice->min_price, 'max_price' => $transferStorePrice->max_price, 'walkin_price' => $transferStorePrice->walkin_price, 'updated_at' => Carbon::now()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['response' => "Stock Movement Failed", 'status' => false]);
        }
        DB::commit();
        return response()->json(['response' => "Stock Moved Successfully", 'status' => true]);
    }

    public function movementHistory($date)
    {
        return Stock_movement::with(['item', 'qtyType', 'transferStore', 'receiveStore', 'user'])->where(function ($q) {
            $q->where('merchant_id', Auth::user()->merchant_id);
        })->where(DB::raw('date(created_at)'), $date)->orderBy("stock_movement_id", "DESC")->get();
    }
}
