<?php

use App\Http\Controllers\ManagerApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', [ManagerApiController::class, 'user']);
Route::get('/ksw/{token}', [ManagerApiController::class, '_index']);
Route::get('/dashboard/{date}', [ManagerApiController::class, 'dashboard']);
Route::get('/getitemcategory', [ManagerApiController::class, 'getItemCategory']);
Route::get('/inventory', [ManagerApiController::class, 'inventory']);
Route::get('/selectedinv/{id}', [ManagerApiController::class, 'selectedInv']);
Route::post('/updateselectedinventory', [ManagerApiController::class, 'updateSelectedInventory']);
Route::post('/updatestocktransferunit', [ManagerApiController::class, 'updateStockTransferUnit']);
Route::post('/updateprice', [ManagerApiController::class, 'updatePrice']);
// Route::post('/updatestock', [ManagerApiController::class, 'updateStock']);
Route::post('/additem', [ManagerApiController::class, 'addItem']);
Route::get('/getsuppliers', [ManagerApiController::class, 'getSuppliers']);
Route::post('/addsupplier', [ManagerApiController::class, 'addSupplier']);
Route::post('/updatesupplier', [ManagerApiController::class, 'updateSupplier']);
Route::get('/getpurchasedata', [ManagerApiController::class, 'getPurchaseData']);
Route::post('/savepurchase', [ManagerApiController::class, 'savePurchase']);
Route::get('/getpendingpurchases', [ManagerApiController::class, 'getPendingPurchases']);
Route::post('/confirmdelivery', [ManagerApiController::class, 'confirmDelivery']);
Route::post('/transferstock', [ManagerApiController::class, 'transferStock']);
Route::get('/getcustomers', [ManagerApiController::class, 'getCustomers']);
Route::get('/geteachcustomer/{id}', [ManagerApiController::class, 'getEachcustomer']);
Route::post('/addcustomer', [ManagerApiController::class, 'addCustomer']);
Route::post('/updatecustomer', [ManagerApiController::class, 'updateCustomer']);
Route::post('/creditprocess', [ManagerApiController::class, 'creditprocess']);
Route::post('/paydebit', [ManagerApiController::class, 'payDebit']);
Route::post('/paydiscount', [ManagerApiController::class, 'payDiscount']);
Route::post('/adddiscount', [ManagerApiController::class, 'addDiscount']);
Route::post('/deldiscount', [ManagerApiController::class, 'delDiscount']);
Route::get('/getusers', [ManagerApiController::class, 'getUsers']);
Route::get('/getroles', [ManagerApiController::class, 'getRoles']);
Route::post('/adduser', [ManagerApiController::class, 'addUser']);
Route::get('/getuser/{userId}', [ManagerApiController::class, 'getUser']);
Route::get('/userstatus/{userId}/{status}', [ManagerApiController::class, 'userStatus']);
Route::get('/managersaleshistory/{date}', [ManagerApiController::class, 'salesHistory']);
Route::get('/managertransferhistory/{date}', [ManagerApiController::class, 'transferHistory']);
Route::get('/managerpurchasehistory/{date}', [ManagerApiController::class, 'purchaseHistory']);
Route::post('/canclesalesorder', [ManagerApiController::class, 'cancleSalesOrder']);
Route::get('/canclepurchase/{purchaseId}/{itemId}/{time}', [ManagerApiController::class, 'canclePurchase']);
Route::get('/cancleallpurchase/{purchaseId}/{time}', [ManagerApiController::class, 'cancleAllPurchase']);
Route::get('/cancletransfer/{transferId}/{time}', [ManagerApiController::class, 'cancleTransfer']);
Route::get('/confirmtransfer/{transferId}/{time}', [ManagerApiController::class, 'confirmTransfer']);
Route::get('/cancleeachtransfer/{transferId}/{itemId}/{time}', [ManagerApiController::class, 'cancleEachTransfer']);
Route::get('/confirmeachtransfer/{transferId}/{itemId}/{time}', [ManagerApiController::class, 'confirmEachTransfer']);
Route::get('/clearusersession/{userId}', [ManagerApiController::class, 'clearUserSession']);
Route::get('/reprintreceipt/{orderId}', [ManagerApiController::class, 'reprintReceipt']);
Route::post('/topsales', [ManagerApiController::class, 'topSales']);
Route::get('/gettopquantity', [ManagerApiController::class, 'getTopQuantity']);
Route::post('/topcustomersvolume', [ManagerApiController::class, 'topCustomersVolume']);
Route::post('/topcustomersamount', [ManagerApiController::class, 'topCustomersAmount']);
Route::post('/topcustomerstickets', [ManagerApiController::class, 'topCustomersTickets']);
Route::post('/createstore', [ManagerApiController::class, 'createStore']);
Route::get('/fetchstore', [ManagerApiController::class, 'fetchStore']);
Route::post('/switchstore', [ManagerApiController::class, 'switchStore']);
Route::get('/stockmovementdata', [ManagerApiController::class, 'stockMovementData']);
Route::post('/stockmovement', [ManagerApiController::class, 'stockMovement']);
Route::get('/stockmovementhistory/{date}', [ManagerApiController::class, 'movementHistory']);