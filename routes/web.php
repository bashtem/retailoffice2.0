<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagersController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StocksController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', [HomeController::class, 'index']);
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [HomeController::class, 'logout']);


// Manager Routes...............................

Route::get('/dashboard', [ManagersController::class, 'dashboard']);
Route::get('/inventory', [ManagersController::class, 'inventory']);
Route::get('/purchase', [ManagersController::class, 'purchase']);
Route::get('/managertransfer', [ManagersController::class, 'managerTransfer']);
Route::get('/manageusers', [ManagersController::class, 'manageUsers']);
Route::get('/report', [ManagersController::class, 'report']);
Route::get('/managerhistory', [ManagersController::class, 'history']);
Route::get('/customers', [ManagersController::class, 'customers']);
Route::get('/purchasedata', [ManagersController::class, 'purchaseData']);
Route::get('/items', [ManagersController::class, 'items']);
Route::get('/suppliers', [ManagersController::class, 'suppliers']);
Route::post('/addSupplier', [ManagersController::class, 'addSupplier']);
Route::post('/addItem', [ManagersController::class, 'addItem']);
Route::post('/savePurchase', [ManagersController::class, 'savePurchase']);
Route::get('/fetchprice/{id}/{qtyId}', [ManagersController::class, 'fetchPrice']);
Route::post('/updateprice', [ManagersController::class, 'updatePrice']);
Route::post('/saveqtyconversion', [ManagersController::class, 'saveQtyConversion']);
Route::post('/delqtyconversion', [ManagersController::class, 'delQtyConversion']);
Route::get('/fetchitemsdata', [ManagersController::class, 'itemsData']);
Route::get('/fetchconvertdata', [ManagersController::class, 'fetchConvertData']);
Route::get('/fecthqtyconversion', [ManagersController::class, 'fecthQtyConversion']);
Route::get('/managersaleshistory/{date}', [ManagersController::class, 'salesHistory']);
Route::get('/managertransferhistory/{date}', [ManagersController::class, 'transferHistory']);
Route::get('/managerpurchasehistory/{date}', [ManagersController::class, 'purchaseHistory']);
Route::get('/dashboarddata/{date}', [ManagersController::class, 'dashboardData']);
Route::get('/canclesalesorder/{orderId}/{time}', [ManagersController::class, 'cancleSalesOrder']);
Route::get('/canclepurchase/{purchaseId}/{itemId}/{time}', [ManagersController::class, 'canclePurchase']);
Route::get('/cancleallpurchase/{purchaseId}/{time}', [ManagersController::class, 'cancleAllPurchase']);
Route::get('/storeqty/{itemId}', [ManagersController::class, 'storeQty']);
Route::post('/transferstock', [ManagersController::class, 'transferStock']);
Route::get('/confirmtransferdatas', [ManagersController::class, 'confirmTransferDatas']);
Route::get('/cancletransfer/{transferId}/{time}', [ManagersController::class, 'cancleTransfer']);
Route::get('/confirmtransfer/{transferId}/{time}', [ManagersController::class, 'confirmTransfer']);
Route::get('/cancleeachtransfer/{transferId}/{itemId}/{time}', [ManagersController::class, 'cancleEachTransfer']);
Route::get('/confirmeachtransfer/{transferId}/{itemId}/{time}', [ManagersController::class, 'confirmEachTransfer']);
Route::get('/users', [ManagersController::class, 'Users']);
Route::get('/fetchdashqty/{qtyId}', [ManagersController::class, 'fetchDashQty']);
Route::get('/qtytypes', [ManagersController::class, 'qtyTypes']);
Route::get('/inventoryitems', [ManagersController::class, 'inventoryItems']);
Route::get('/selectedInv/{itemId}', [ManagersController::class, 'selectedInv']);
Route::post('/removeitem', [ManagersController::class, 'removeItem']);
Route::get('/fetchusers', [ManagersController::class, 'fetchUsers']);
Route::get('/userstatus/{userid}/{status}', [ManagersController::class, 'userStatus']);
Route::get('/fetchroles', [ManagersController::class, 'fetchRoles']);
Route::get('/fetchpayment', [ManagersController::class, 'fetchPayment']);
Route::post('/newuser', [ManagersController::class, 'newUser']);
Route::post('/edituser', [ManagersController::class, 'editUser']);
Route::get('/customerdatas', [ManagersController::class, 'fetchCustomers']);
Route::post('/addcustomer', [ManagersController::class, 'addCustomer']);
Route::post('/editcustomer', [ManagersController::class, 'editCustomer']);
Route::post('/creditprocess', [ManagersController::class, 'creditProcess']);
Route::post('/creditorders', [ManagersController::class, 'creditOrders']);
Route::post('/paydebit', [ManagersController::class, 'payDebit']);
Route::post('/fetchcredit', [ManagersController::class, 'fetchCredit']);
Route::post('/paydiscount', [ManagersController::class, 'payDiscount']);
Route::post('/fetchdiscountbal', [ManagersController::class, 'fetchDiscountBal']);
Route::post('/adddiscount', [ManagersController::class, 'addDiscount']);
Route::post('/discountitems', [ManagersController::class, 'discountItems']);
Route::post('/deldiscount', [ManagersController::class, 'delDiscount']);
Route::post('/salesreport', [ManagersController::class, 'salesReport']);
Route::get('/downloadreport/{fromdate}/{toDate}/{reportType}/{qtyId}', [ManagersController::class, 'downloadReport']);
Route::post('/itemqtyhistory', [ManagersController::class, 'itemQtyHistory']);
Route::get('/downloaditemqtyhistory/{fromdate}/{toDate}/{qtyId}/{itemId}', [ManagersController::class, 'downloadItemQtyHistory']);


// Stock Keeper Routes.........................

Route::get('/stockout', [StocksController::class, 'stockOut']);
Route::get('/history', [StocksController::class, 'history']);
Route::get('/stocktransfer', [StocksController::class, 'stockTransfer']);
Route::get('/confirmpurchase', [StocksController::class, 'confirmPurchasePage']);
Route::get('/fetchpurchase', [StocksController::class, 'fetchPurchase']);
Route::get('/fetchoutstanding', [StocksController::class, 'fetchOutstanding']);
Route::get('/fetchtransferdata', [StocksController::class, 'fetchTransferData']);
Route::post('/fetchtransferqty', [StocksController::class, 'fetchTransferQty']);
Route::post('/confirmoutstanding', [StocksController::class, 'confirmOutstanding']);
Route::post('/increasestock', [StocksController::class, 'increaseStock']);
Route::post('/transferrequest', [StocksController::class, 'transferRequest']);
Route::get('/itemsstockout', [StocksController::class, 'itemsStockOut']);
Route::post('/confirmstockout', [StocksController::class, 'confirmStockOut']);


// Sales Attendant Routes......................

Route::post('/savesales', [SalesController::class, 'saveSales']);
Route::post('/savecustomer', [SalesController::class, 'saveCustomer']);
Route::get('/fetchitems', [SalesController::class, 'fetchItems']);
Route::get('/fetchqtytype', [SalesController::class, 'fetchQtyType']);
Route::get('/fetchcustomers', [SalesController::class, 'fetchCustomers']);
Route::get('/fetchpaymenttypes', [SalesController::class, 'fetchPaymentTypes']);
Route::post('/fetchdiscount', [SalesController::class, 'fetchDiscount']);
Route::post('/processorder', [SalesController::class, 'processOrder']);
Route::get('/fetchqtydata/{id}/{qtyId}', [SalesController::class, 'fetchQtyData']);
Route::get('/saleshistory/{date}', [SalesController::class, 'salesHistory']);
Route::get('/reprintreceipt/{orderId}', [SalesController::class, 'reprintReceipt']);
