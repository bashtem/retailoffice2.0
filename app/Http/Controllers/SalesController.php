<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EnumController as Enum;
use App\Http\Middleware\Sales;
use App\Models\Credit;
use App\Models\Credit_log;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Discount_item;
use App\Models\Discount_log;
use App\Models\Item;
use App\Models\Item_price;
use App\Models\Item_qty;
use App\Models\Item_qty_log;
use App\Models\Item_tiered_price;
use App\Models\Merchant_store;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Payment_type;
use App\Models\Qty_type;
use App\Models\Transaction_type;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SalesController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(Sales::class),
        ];
    }

    public function receiptFn($datas, $bottomTitle, $onlyUnit = false, $duplicate = false)
    {
        $header = ($onlyUnit) ? 'ITEM' . "\x09" . "\x09" . "\x09" . 'QTY' . "\x09" . 'UNIT COST' . "\x09" . "\n" : 'ITEM' . "\x09" . "\x09" . "\x09" . 'QTY' . "\x09" . 'UNIT COST' . "\x09" . 'AMOUNT' . "\n";
        $duplicateHead = '';
        $reprintDate = '';
        if($duplicate){
            $duplicateHead = "\n"."\x1B" . "\x61" . "\x31".'-----------------------------------------------' . "\n" . 'DUPLICATE RECEIPT' . "\n". '-----------------------------------------------'. "\n";
            $reprintDate = 'REPRINT DATE:  ' . date("d-m-Y") . ' ' . date('h:i:s') . "\n";
        }
        return [
            "\x1B" . "\x40",          // init
            "\x1B" . "\x61" . "\x31", // center align
            $duplicateHead,
            $datas['merchantTitle'] . "\n",
            "\x1B" . "\x61" . "\x30", // left align
            $datas['merchantAddress'] . "\n",     // text and line break
            'TELEPHONE: ' . $datas['merchantPhone'] . "\n",    // line break
            "\n",
            "\x1B" . "\x61" . "\x31", // center align
            '-----------------------------------------------' . "\n",
            'OFFICIAL RECEIPT' . "\n",
            '-----------------------------------------------' . "\n",
            "\x1B" . "\x61" . "\x30", // left align
            'CUSTOMER:      ' . $datas['cusName'] . "\n",
            'DATE:          ' . $datas['orderDate'] . ' ' . $datas['orderTime'] . "\n",
            $reprintDate,
            'INVOICE NO:    ' . $datas['invoiceNumber'] . "\n",
            'SERIAL NO:     ' . $datas['serialNumber'] . "\n",
            'CASHIER:       ' . $datas['cashier'] . "\n",
            '------------------------------------------------' . "\n",
            $header,
            '------------------------------------------------' . "\n",
            $datas['items'],
            '------------------------------------------------' . "\n",
            'GROSS TOTAL:   ' . $datas['totalAmount'] . '  NGN' . "\n",
            'DISCOUNT:      ' . $datas['totalDiscount'] . "\n",
            'NET TOTAL:     ' . $datas['netTotal'] . "\n",
            '------------------------------------------------' . "\n",
            'THANKS FOR PATRONISING ' . $datas['merchantTitle'] . "\n",
            '------------------------------------------------' . "\n",
            'NO REFUNDS AFTER PAYMENT' . "\n",
            'POWERED BY WIREPICK NIGERIA LIMITED' . "\n",
            '------------ ' . $bottomTitle . ' ------------' . "\n",
            "\n",
            "\n",
            "\n",
            "\n",
            "\n",
            "\x1B" . "\x69"          // cut paper
        ];
    }

    public function saveCustomer(Request $req)
    {
        $this->validate($req, [
            'cusName' => 'required',
            'cusMobile' => 'required',
            'cusAddress' => 'required',
            'cusPayment' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $cusMail = $req->cusMail ?? '-';
            $id = Customer::insertGetId(['merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'registered_by_user_id' => Auth::user()->user_id, 'cus_name' => $req->cusName, 'cus_mobile' => $req->cusMobile, 'cus_mail' => $cusMail, 'cus_address' => $req->cusAddress, 'payment_id' => $req->cusPayment, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'cus_id');
            Credit::insert(['cus_id' => $id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            Discount::insert(['cus_id' => $id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            DB::commit();
            return redirect('/')->with("info", "modalNotificate('Customer Added Successfully!')");
        } catch (\PDOException $e) {
            DB::rollback();
            if ($e->errorInfo[1] == 1062)
                return redirect('/')->with("info", "modalNotificate('Customer Already Exist!')");
            else
                return redirect('/')->with("info", "modalNotificate('Customer Registration Failed')");
        }
    }

    public function fetchCustomers()
    {
        $customers = Customer::with('credit')->where("customers.store_id", Auth::user()->store_id)->get();
        return $customers;
    }

    public function fetchPaymentTypes()
    {
        $payment = Payment_type::all();
        return $payment;
    }

    public function fetchItems()
    {
        $items = Item::join('item_qties', 'items.item_id', '=', 'item_qties.item_id')->where('item_qties.qty_id', '=', '1')->where("item_qties.store_id", Auth::user()->store_id)->get();
        return $items;
    }

    public function fetchQtyType()
    {
        $qty = Qty_type::all();
        return response()->json($qty);
    }

    public function fetchQtyData($id, $qtyId)
    {
        $itemQty = Item_qty::where(['item_id' => $id, 'qty_id' => $qtyId, 'store_id' => Auth::user()->store_id])->first();
        $itemPrice = Item_price::where(['item_id' => $id, 'qty_id' => $qtyId, 'store_id' => Auth::user()->store_id])->first();
        $tieredPrice = Item_tiered_price::where(['item_id' => $id, 'qty_id' => $qtyId, "store_id" => Auth::user()->store_id])->orderBy('qty', "ASC")->get();
        $itemQty['price'] = $itemPrice;
        $itemQty['tieredPrice'] = $tieredPrice;
        return $itemQty;
    }

    public function fetchDiscount(Request $req)
    {
        $discountItems = Discount_item::where('cus_id', $req['cus_id'])->get();
        return $discountItems;
    }

    public function processOrder(Request $req)
    {
        $receiptDatas = array();
        $order = (array)$req->order;
        $invoiceNumber = 'SO-' . date('ymdhis');
        $orderDatas = array_merge($order, ['merchant_id' => Auth::user()->merchant_id, 'store_id' => Auth::user()->store_id, 'order_no' => $invoiceNumber, 'user_id' => Auth::user()->user_id, 'order_date' => date('Y/m/d'), 'order_total_amount' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $orderArr = $req['orderItems'];
        $orderTotalAmount = 0; // NEW

        DB::beginTransaction();
        try {
            // ORDER
            $order = DB::table('orders')->insertGetId($orderDatas, 'order_id');
            for ($x = 0; $x < count($req['orderItems']); $x++) {
                $itemPrice = Item_price::where(['item_id' => $orderArr[$x]['itemId'], 'qty_id' => $req['order']['qty_id'], 'store_id' => Auth::user()->store_id])->first();  // NEW
                if (($orderArr[$x]['tieredPriceId'] != null) && ($req->cusType != Enum::WALKIN)) {
                    $tieredPrice = Item_tiered_price::where('id', $orderArr[$x]['tieredPriceId'])->where('item_id', $orderArr[$x]['itemId'])->where('store_id', Auth::user()->store_id)->first();
                    $price = $tieredPrice->price;
                } else {
                    if (($req->cusType == Enum::WALKIN) && ($itemPrice->walkin_price > 0))
                        $price = $itemPrice->walkin_price;
                    else
                        $price = $itemPrice->max_price;
                }
                $amountEachItem = $price * $orderArr[$x]['qty'];  // NEW
                $orderItems[] = ['order_id' => $order, 'item_id' => $orderArr[$x]['itemId'], 'quantity' => $orderArr[$x]['qty'], 'cost_price' => $itemPrice->price, 'price' => $price, 'amount' => $amountEachItem, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                $orderTotalAmount += $amountEachItem; // NEW
            }
            Order::where('order_id', $order)->update(['order_total_amount' => $orderTotalAmount]);
            Order_item::insert($orderItems);

            // ITEM QUANTITY
            $transId = Transaction_type::where('trans_desc', Enum::STOCK_OUT)->first();
            for ($x = 0; $x < count($req['orderItems']); $x++) {
                $itemQty = item_qty::where('item_id', $req['orderItems'][$x]['itemId'])->where('qty_id', $req['order']['qty_id'])->where(['store_id' => Auth::user()->store_id])->first();
                $oldQty = $itemQty->quantity;
                $newQty = $oldQty - $req['orderItems'][$x]['qty'];
                if ($newQty < 0) {
                    throw "e";
                }
                $itemQty->update(['quantity' => $newQty, 'updated_at' => Carbon::now()]);
                Item_qty_log::insert(['store_id' => Auth::user()->store_id, 'user_id' => Auth::user()->user_id, 'qty_id' => $req['order']['qty_id'], 'item_id' => $req['orderItems'][$x]['itemId'], 'old_qty' => $oldQty, 'new_qty' => $newQty, 'trans_id' => $transId->trans_id, 'date' => date('Y/m/d'), 'time' => $req['order']['order_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }

            // DISCOUNT
            if ($req['totalDiscount'] > 0) {
                $discount = Discount::where('cus_id', $req['order']['cus_id'])->first();
                $newDiscount = $discount->discount_credit + $req['totalDiscount'];
                Discount::where('cus_id', $req['order']['cus_id'])->update(['discount_credit' => $newDiscount, 'updated_at' => Carbon::now()]);
                Discount_log::create(['order_id' => $order, 'total_discount' => $req->totalDiscount, 'date' => date('Y/m/d'), 'time' => $req['order']['order_time'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }

            // CREDIT
            $payment = payment_type::where('payment_id', $req['order']['payment_id'])->first();
            if ($payment->payment_desc == Enum::CREDIT) {
                $credit = Credit::where('cus_id', $req['order']['cus_id'])->first();
                $availCredit = $credit->available_credit;
                $outCredit = $credit->out_credit;
                $newAvail = ($availCredit) - ($req['order']['order_total_amount']);
                $newOut = ($outCredit) + ($req['order']['order_total_amount']);
                Credit::where('cus_id', $req['order']['cus_id'])->update(['available_credit' => $newAvail, 'out_credit' => $newOut, 'updated_at' => Carbon::now()]);
                $creditOrderId = DB::table('credit_orders')->insertGetId(['order_id' => $order, 'credit_id' => $credit->credit_id, 'cus_id' => $req['order']['cus_id'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 'credit_order_id');
                Credit_log::insert(['credit_order_id' => $creditOrderId, 'user_id' => Auth::user()->user_id, 'credit_log_status' => Enum::ORDER, 'old_credit' => $availCredit, 'new_credit' => $newAvail, 'credit_date' => date('Y/m/d'), 'credit_time' => $req['order']['order_time'], 'credit_id' => $credit->credit_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }

            // RECEIPT
            $itemsReceipt = '';
            $itemReceiptNoUnit = '';
            $receiptDatas = [];
            for ($x = 0; $x < count($req['orderItems']); $x++) {
                if (($orderArr[$x]['tieredPriceId'] != null) && ($req->cusType != Enum::WALKIN)) {
                    $tieredPrice = Item_tiered_price::where('id', $orderArr[$x]['tieredPriceId'])->where('item_id', $orderArr[$x]['itemId'])->where('store_id', Auth::user()->store_id)->first();
                    $price = $tieredPrice->price;
                } else {
                    $itemPrice = Item_price::where(['item_id' => $orderArr[$x]['itemId'], 'qty_id' => $req['order']['qty_id'], 'store_id' => Auth::user()->store_id])->first();
                    if (($req->cusType == Enum::WALKIN) && ($itemPrice->walkin_price > 0))
                        $price = $itemPrice->walkin_price;
                    else
                        $price = $itemPrice->max_price;
                }
                $amount = $price * $orderArr[$x]['qty'];    // NEW
                $strlen = 20 - strlen($req['orderItems'][$x]['name']);
                $qty = $req['orderItems'][$x]['qty'];
                $itemsReceipt .= '' . str_pad(trim(strtoupper($req['orderItems'][$x]['name'])), $strlen) . "\x09" . round($qty, 3) . "\x09" . round($price, 2) . "\x09" .round($amount, 2) . "\x0A";
                $itemReceiptNoUnit .= '' . str_pad(trim(strtoupper($req['orderItems'][$x]['name'])), $strlen) . "\x09" . round($qty, 3) . "\x09" . round($price, 2) . "\x09" . "\x0A";
            }

            $netTotal = $orderTotalAmount; //+ $req['totalDiscount'];   // NEW
            $merchant = Merchant_store::where('store_id', Auth::user()->store_id)->first();
            $printCopy = ['TELLER COPY', 'CUSTOMER COPY', 'STOCK KEEPER COPY'];
            $orderCount = Order::where(['store_id' => Auth::user()->store_id, 'order_date' => Carbon::now()->toDateString()])->count();
            Order::where('order_id', $order)->update(['serial_no' => $orderCount]);
            foreach ($printCopy as $key => $value) {
                $receiptFnData = [
                    'merchantTitle' => $merchant->title,
                    'merchantAddress' => $merchant->address,
                    'merchantPhone' => $merchant->telephone,
                    'cusName' => $req->cus_name,
                    'orderTime' => $req['order']['order_time'],
                    'orderDate' => date("d-m-Y"),
                    'invoiceNumber' => $invoiceNumber,
                    'cashier' => Auth::user()->name,
                    'items' => (count($printCopy) == ($key + 1)) ? $itemReceiptNoUnit : $itemsReceipt,
                    'totalAmount' => number_format($orderTotalAmount, 2, '.', ''), 
                    'totalDiscount' => number_format($req['totalDiscount'], 2, '.', ''),
                    'netTotal' => number_format($netTotal, 2, '.', ''),
                    'serialNumber' => $orderCount
                ];
                $receiptDatas[] = (count($printCopy) == ($key + 1)) ? $this->receiptFn($receiptFnData, $value, true) : $this->receiptFn($receiptFnData, $value);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return $receiptDatas;
    }

    public function reprintReceipt($orderId)
    {
        $itemsReceipt = '';
        $itemReceiptNoUnit = '';
        $printCopy = ['TELLER COPY', 'CUSTOMER COPY', 'STOCK KEEPER COPY'];
        $merchant = Merchant_store::where('store_id', Auth::user()->store_id)->first();
        $order = Order::with(['items', 'items.item', 'discount', 'cus', 'user'])->where('order_id', $orderId)->first();
        $order->items->each(function ($item, $key) use (&$itemsReceipt, &$itemReceiptNoUnit) {
            $price = implode('', explode(',', $item['price']));
            $amount = implode('', explode(',', $item['amount']));
            $strlen = 20 - strlen($item['item']['item_name']);
            $itemsReceipt .= '' . str_pad(trim(strtoupper($item['item']['item_name'])), $strlen) . "\x09" . round($item['quantity'], 3) . "\x09" . round($price, 2) . "\x09" . round($amount, 2) . "\x0A";
            $itemReceiptNoUnit .= '' . str_pad(trim(strtoupper($item['item']['item_name'])), $strlen) . "\x09" . round($item['quantity'], 3) . "\x09" . round($price, 2) . "\x09" . "\x0A";
        });
        foreach ($printCopy as $key => $value) {
            $receiptFnData = [
                'merchantTitle' => $merchant->title,
                'merchantAddress' => $merchant->address,
                'merchantPhone' => $merchant->telephone,
                'cusName' => $order->cus->cus_name,
                'orderTime' => $order->order_time,
                'orderDate' => date("d-m-Y"),
                'invoiceNumber' => $order->order_no,
                'cashier' => $order->user->name,
                'items' => (count($printCopy) == ($key + 1)) ? $itemReceiptNoUnit : $itemsReceipt,
                'totalAmount' => number_format($order->order_total_amount, 2, '.', ''),
                'totalDiscount' => number_format(($order->discount->total_discount) ?? 0,  2, '.', ''),
                'netTotal' => number_format($order->order_total_amount, 2, '.', ''),
                'serialNumber' => $order->serial_no
            ];
            $receiptDatas[] = (count($printCopy) == ($key + 1)) ? $this->receiptFn($receiptFnData, $value, true, true) : $this->receiptFn($receiptFnData, $value, false, true);
        }
        Order::where('order_id', $orderId)->update(['receipt_printed' => 1]);
        return $receiptDatas;
    }

    public function salesHistory($date)
    {
        return Order::with(['cus', 'payment', 'qty', 'items.item'])->orderBy('order_id', 'DESC')->where('order_date', $date)->where('order_status', "!=", Enum::CANCLED)->where('store_id', Auth::user()->store_id)->get();
    }
}
