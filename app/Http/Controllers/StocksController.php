<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EnumController as Enum;
use App\Http\Controllers\ManagersController as Manager;
use App\Http\Middleware\Stock;
use App\Models\Order;
use App\Models\Order_confirm;
use App\Models\Purchase_order;
use App\Models\Quantity_conversion;
use App\Models\Transaction_type;
use App\Models\Transfer_item;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class StocksController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(Stock::class, except: ['fetchTransferData', 'fetchTransferQty']),
        ];
    }

    public function stockOut()
    {
        return view('stock.stockout');
    }

    public function stockTransfer()
    {
        return view('stock.stock_transfer');
    }

    public function confirmPurchasePage()
    {
        return view('stock.confirm_purchase');
    }

    public function fetchPurchase()
    {
        $purchases = Purchase_order::with(['Purchase_order_item.items', 'Purchase_order_item' => function ($query) {
            $query->where("purchase_status", "PENDING")->get();
        }, 'Qty', 'Supplier'])->where('purchase_orders.store_id', Auth::user()->store_id)->whereHas('Purchase_order_item', function ($query) {
            $query->where("purchase_status", "PENDING");
        })->orderBy("purchase_id", "DESC")->get();
        return ($purchases->toArray());
    }

    public function fetchOutstanding()
    {
        $outstandingPurchase = Purchase_order::with(["Purchase_excess_out" => function ($query) {
            $query->where("status", "PENDING");
        }, "Purchase_excess_out.Purchase_order_item.items", 'Qty', 'Supplier'])->where('purchase_orders.store_id', Auth::user()->store_id)->whereHas("Purchase_excess_out", function ($query) {
            $query->where("status", "PENDING");
        })->get();
        return ($outstandingPurchase->toArray());
    }

    public function confirmOutstanding(Request $req)
    {
        DB::beginTransaction();
        try {
            $tranType = Transaction_type::where("trans_desc", "PURCHASE")->first()->trans_id;
            for ($x = 0; $x < count($req->all()); $x++) {
                $qty = DB::table('item_qties')->where([["item_id", '=', $req[$x]['purchase_order_item']['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->first();
                $newQty =  ($req[$x]['confirmQty']) + ($qty->quantity);
                DB::table("item_qties")->where([["item_id", '=', $req[$x]['purchase_order_item']['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->update(["quantity" => $newQty]);
                DB::table("item_qty_logs")->insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $req[$x]['qtyId'], "item_id" => $req[$x]['purchase_order_item']['item_id'], "old_qty" => $qty->quantity, "new_qty" => $newQty, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                // DB::
                $outLeft = ($req[$x]['qty']) - ($req[$x]["confirmQty"]);
                $oldOutQty = DB::table('purchase_excess_outs')->where("excess_out_id", $req[$x]['excess_out_id'])->first();
                if ($outLeft == 0) {
                    DB::table("purchase_excess_outs")->where("excess_out_id", $req[$x]['excess_out_id'])->update(["qty" => $outLeft, "status" => Enum::SUCCESS, "updated_at" => Carbon::now()]);
                    DB::table("purchase_excess_out_logs")->insert(["user_id" => Auth::user()->user_id, "excess_out_id" => $req[$x]['excess_out_id'], "old_qty" => $oldOutQty->qty, "new_qty" => $outLeft, "status" => Enum::SUCCESS, "log_date" => date("Y/m/d"), "log_time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                } else {
                    DB::table("purchase_excess_outs")->where("excess_out_id", $req[$x]['excess_out_id'])->update(["qty" => $outLeft, "status" => Enum::PENDING, "updated_at" => Carbon::now()]);
                    DB::table("purchase_excess_out_logs")->insert(["user_id" => Auth::user()->user_id, "excess_out_id" => $req[$x]['excess_out_id'], "old_qty" => $oldOutQty->qty, "new_qty" => $outLeft, "status" => Enum::PENDING, "log_date" => date("Y/m/d"), "log_time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(["success" => true]);
    }

    public function increaseStock(Request $req)
    {

        DB::beginTransaction();
        try {
            $tranType = transaction_type::where("trans_desc", "PURCHASE")->first()->trans_id;
            for ($x = 0; $x < count($req->all()); $x++) {

                DB::table("purchase_order_items")->where("purchase_item_id", $req[$x]['purchase_item_id'])->update(["confirm_user_id" => Auth::user()->user_id, "purchase_status" => Enum::SUCCESS, "confirm_date" => date("Y/m/d"), "confirm_time" => $req[$x]["confirmTime"]]);
                $qty = DB::table('item_qties')->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->first();
                $newQty =  ($req[$x]['purchase_qty']) + ($qty->quantity);

                if ($req[$x]['outExcess']['type'] == Enum::EXCESS) {

                    DB::table("item_qties")->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->update(["quantity" => $newQty]);
                    DB::table("item_qty_logs")->insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $req[$x]['qtyId'], "item_id" => $req[$x]['item_id'], "old_qty" => $qty->quantity, "new_qty" => $newQty, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                    $qtyPurchase = DB::table('item_qties')->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->first();
                    $newQtyExcess =  ($req[$x]['outExcess']['qty']) + ($qtyPurchase->quantity);
                    DB::table("item_qties")->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->update(["quantity" => $newQtyExcess]);
                    DB::table("item_qty_logs")->insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $req[$x]['qtyId'], "item_id" => $req[$x]['item_id'], "old_qty" => $qtyPurchase->quantity, "new_qty" => $newQtyExcess, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                    $excessId = DB::table("purchase_excess_outs")->insertGetId(["purchase_id" => $req[$x]['purchase_id'], "purchase_item_id" => $req[$x]['purchase_item_id'], "qty" => $req[$x]['outExcess']['qty'], "type" => Enum::EXCESS, "status" => Enum::SUCCESS, "created_at" => Carbon::now(), "updated_at" => Carbon::now()], "excess_out_id");
                    DB::table("purchase_excess_out_logs")->insert(["user_id" => Auth::user()->user_id, "excess_out_id" => $excessId, "old_qty" => 0, "new_qty" => $req[$x]['outExcess']['qty'], "status" => Enum::SUCCESS, "log_date" => date("Y/m/d"), "log_time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                } else if ($req[$x]['outExcess']['type'] == Enum::OUTSTANDING) {

                    $outQty = $req[$x]['outExcess']['qty'];
                    $newQtyOut = ($req[$x]['purchase_qty'] - $outQty) + $qty->quantity;
                    DB::table("item_qties")->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->update(["quantity" => $newQtyOut]);
                    DB::table("item_qty_logs")->insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $req[$x]['qtyId'], "item_id" => $req[$x]['item_id'], "old_qty" => $qty->quantity, "new_qty" => $newQtyOut, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                    $outId = DB::table("purchase_excess_outs")->insertGetId(["purchase_id" => $req[$x]['purchase_id'], "purchase_item_id" => $req[$x]['purchase_item_id'], "qty" => $req[$x]['outExcess']['qty'], "type" => Enum::OUTSTANDING, "status" => Enum::PENDING, "created_at" => Carbon::now(), "updated_at" => Carbon::now()], "excess_out_id");
                    DB::table("purchase_excess_out_logs")->insert(["user_id" => Auth::user()->user_id, "excess_out_id" => $outId, "old_qty" => 0, "new_qty" => $req[$x]['outExcess']['qty'], "status" => Enum::PENDING, "log_date" => date("Y/m/d"), "log_time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                } else {
                    DB::table("item_qties")->where([["item_id", '=', $req[$x]['item_id']], ["qty_id", '=', $req[$x]['qtyId']]])->where(["store_id" => Auth::user()->store_id])->update(["quantity" => $newQty]);
                    DB::table("item_qty_logs")->insert(["user_id" => Auth::user()->user_id, "store_id" => Auth::user()->store_id, "qty_id" => $req[$x]['qtyId'], "item_id" => $req[$x]['item_id'], "old_qty" => $qty->quantity, "new_qty" => $newQty, "trans_id" => $tranType, "date" => date("Y/m/d"), "time" => $req[$x]['confirmTime'], "created_at" => Carbon::now(), "updated_at" => Carbon::now()]);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(["success" => true]);
    }

    public function fetchTransferData()
    {
        return Manager::fetchConvertData();
    }

    public function fetchTransferQty(Request $req)
    {
        $datas = Quantity_conversion::where([["item_id", '=', $req['item_id']], ["initial_qty_id", '=', $req['initial_qty_id']], ["converted_qty_id", '=', $req['converted_qty_id']]])->where(["store_id" => Auth::user()->store_id])->first();
        if (count($datas) > 0) {
            // $srcQty = item_qty::where([["item_id",'=',$req['item_id']],["qty_id",'=',$req['initial_qty_id']]])->first();
            // if($req['initial_qty'] > $srcQty->quantity){
            //     return 'Stock Quantity Exceeded';
            // }else{
            $datas['trnQty'] = round((($req['initial_qty']) * ($datas['converted_qty'])) / ($datas['initial_qty']), 4);
            return $datas;
            // }
        }
        return;
    }

    public function transferRequest(Request $req)
    {
        DB::beginTransaction();
        try {
            $transferId = DB::table('transfer_orders')->insertGetid(['user_id_transfer' => Auth::user()->user_id, "merchant_id" => Auth::user()->merchant_id, "store_id" => Auth::user()->store_id, 'transfer_date' => date('Y/m/d'), 'transfer_time' => $req['order']['transfer_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'transfer_id');
            for ($x = 0; $x < count($req['items']); $x++) {
                Transfer_item::insert(['transfer_id' => $transferId, 'conversion_id' => $req['items'][$x]['conversion_id'], 'item_id' => $req['items'][$x]['item_id'], 'transfer_qty' => $req['items'][$x]['transfer_qty'], 'transferred_qty' => $req['items'][$x]['transferred_qty'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return "Transfer Requested Successfully";
    }

    public function itemsStockOut()
    {
        return Order::with(['cus', 'payment', 'qty', 'items.item'])->orderBy('order_id', 'DESC')->where('order_status', Enum::PENDING)->where(["orders.store_id" => Auth::user()->store_id])->get();
    }

    public function confirmStockOut(Request $req)
    {
        DB::beginTransaction();
        try {
            $data = new Collection($req);
            $data->map(function ($each) {
                $id = $each['orderId'];
                Order::where('order_id', $id)->update(['order_status' => Enum::SUCCESS]);
                Order_confirm::create(['order_id' => $id, 'user_id' => Auth::user()->user_id, 'confirm_date' => date('Y/m/d'), 'confirm_time' => $each['confirmTime'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            });
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return response()->json(["response" => "Success"]);
    }

    public function history()
    {
        return view('stock.history');
    }
    public function report()
    {
        return view('stock.report');
    }
}
