var salesPortal = angular.module("salesPortal", ['ngAnimate','ui.select2','ui.bootstrap']);
var retailOffice  = angular.module("retailOffice", ['ngAnimate','ui.select2','ui.bootstrap','datatables','ngRoute']);

retailOffice.run(()=>{
        $('.loaders').scheletrone();
})

retailOffice.service("fns",function($http){
        this.getList = function(data, arrayKey, objKey) {
                data[arrayKey].forEach(function(element) {
                     element[objKey] = data[objKey]
                }); 
                return data[arrayKey];
          };

        this.userRoles = function(){
               return $http({method:"GET", url:"fetchroles"}).then((res)=>{
                       return res;
                },(res)=>{console.log(res.data)});
        }
        
        this.payments = function(){
               return $http({method:"GET", url:"fetchpayment"}).then((res)=>{
                       return res;
                },(res)=>{console.log(res.data)});
        }

        this.search = function(searchData, arrayData, arrayKey){
                var searchedData = null;
                if(searchData == null){
                         searchedData = angular.copy(arrayData);
                        return searchedData;
                }
                searchedData = arrayData.filter((each)=>{
                        if(each[arrayKey].toLowerCase().search(searchData.toLowerCase()) >= 0){
                                return each;
                        }
                });
                return searchedData;
        }

        this.qtyTypes = function(){
                return $http({method:"GET", url:"fetchconvertdata"}).then((res)=>{
                        return res;
                 },(res)=>{console.log(res.data)});
        }

})

// TRANSFER STOCK
retailOffice.controller("stocktransfer", function($scope,$http,fns){
        $scope.transferItems ={}; 
        $scope.transferItems.items = [];

        $scope.fetchTranferData = function(){
                var itemCat =[];
                $http({method:"GET",url:"fetchtransferdata"}).then(function(res){
                        var itemData = res.data['items'];
                        $scope.qtyTransData = res.data['qtyTypes'];
                        for(x in itemData){
                                itemCat = itemCat.concat(fns.getList(itemData[x],"items","cat_desc"));
                        }                                                                               
                      $scope.itemsTrans = itemCat;
                },function(res){ console.log(res.data)});
        }
        $scope.fetchTranferData();

        $scope.selTranQty = function(x){
                var trnD =[];
                if(x){
                        var index = $scope.qtyTransData.indexOf(x);
                        trnD = angular.copy($scope.qtyTransData);
                        trnD.splice(index,1);
                        $scope.qtyTransDataC = angular.copy(trnD);
                }else{
                        $scope.srcNumTrn=null;
                        $scope.cnvQtyTrn=null;
                        $scope.cnvNumTrn=null;                 
                }                
        }

        $scope.fetchTrnQty = function(x){
                if(x){
                        $scope.fetchTrndatas = {item_id:$scope.itemTrn.item_id,initial_qty_id:$scope.srcQtyTrn.qty_id, converted_qty_id:$scope.cnvQtyTrn.qty_id, initial_qty:$scope.srcNumTrn}
                        $http({method:"POST",url:"fetchtransferqty", data:$scope.fetchTrndatas}).then(function(res){
                              if(res.data){
                                     $scope.cnvNumTrn = angular.copy(res.data['trnQty']);
                                     $scope.pickedTrn = angular.copy(res.data);
                              }else{ modalNotificate("Item Transfer Unavailable"); }
                        },function(res){ console.log(res.data)});
                }else{
                        $scope.cnvNumTrn=null;                 
                }
        }

        $scope.clr = function(){
                $scope.cnvQtyTrn=null;
                $scope.cnvNumTrn=null;  
                $scope.srcQtyTrn=null;  
                $scope.srcNumTrn=null;  
        }
        
        $scope.addTrnItems = function(){
                var eachtrn = {conversion_id:$scope.pickedTrn.conversion_id, item_id:$scope.itemTrn.item_id, transfer_qty:$scope.srcNumTrn, transferred_qty:$scope.pickedTrn.trnQty, srcType:$scope.srcQtyTrn.qty_desc, trnType:$scope.cnvQtyTrn.qty_desc, item_name:$scope.itemTrn.item_name, src_qty_id:$scope.srcQtyTrn.qty_id, trn_qty_id:$scope.cnvQtyTrn.qty_id };
                if(_.size($scope.transferItems) <= 1){
                        $scope.transferItems.order = {transfer_time:getTime()};
                        $scope.transferItems.items.push(eachtrn);
                }else{
                        var itm = _.find($scope.transferItems.items,{conversion_id:$scope.pickedTrn.conversion_id,item_id:$scope.itemTrn.item_id});
                        if(!itm){
                                $scope.transferItems.items.push(eachtrn);
                        }else{
                                modalNotificate("Item Already Exist",2000);
                        }
                }
        }

        $scope.delTrnItems = function(x){
                $scope.transferItems.items.splice(x,1);
        }

        $scope.transferRequest = function(){
                if(confirm("Do you want to proceed request ?")){
                        $http({method:"POST",url:"transferrequest", data:$scope.transferItems}).then(function(res){
                                modalNotificate(res.data,2000);
                                $scope.transferItems = {};
                                $scope.transferItems.items = [];
                          },function(res){ console.log(res.data)});
                }
        }

        $scope.storeQty = function(itemId){
                $scope.storeQtyData = null;
                $http({method:"GET",url:"storeqty/"+itemId}).then(function(res){
                               $scope.storeQtyData = res.data;
                  },function(res){ console.log(res.data)});
        }

        $scope.transferStock = function(){
                if(confirm("Do you want to proceed Transfer ?")){
                        $http({method:"POST",url:"transferstock", data:$scope.transferItems}).then(function(res){
                                modalNotificate(res.data,2000);
                                $scope.transferItems = {};
                                $scope.transferItems.items = [];
                          },function(res){ console.log(res.data)});
                }
        }

        $scope.confirmTransferDatas = function(){
                $http({method:"GET",url:"confirmtransferdatas"}).then(function(res){
                        $scope.transferDatas = res.data;
                  },function(res){ console.log(res.data)});
        }
        
        $scope.confirmTransfer = function(transferId){
                if(confirm("Do you want to Confirm Transfer?")){
                        $http({method:"GET",url:"confirmtransfer/"+transferId+"/"+getTime()}).then(function(res){
                                modalNotificate(res.data);
                        },function(res){ console.log(res.data)}).then((res)=>{
                                $scope.confirmTransferDatas();
                        });
                }
        }

        $scope.cancleTransfer = function(transferId){
                if(confirm("Do you want to Cancle Transfer?")){
                        $http({method:"GET", url:"cancletransfer/"+transferId+"/"+getTime()}).then((res)=>{
                                modalNotificate(res.data);
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.confirmTransferDatas();
                        })
                }
        }

        $scope.cancleEachTrn = function(transferId,itemId){
                if(confirm("Do you want to Cancle Transfer?")){
                        $http({method:"GET", url:"cancleeachtransfer/"+transferId+"/"+itemId+"/"+getTime()}).then((res)=>{
                                modalNotificate(res.data);
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.confirmTransferDatas();
                        })
                }
        }
        
        $scope.confirmEachTrn = function(transferId,itemId){
                if(confirm("Do you want to Confirm Transfer?")){
                        $http({method:"GET", url:"confirmeachtransfer/"+transferId+"/"+itemId+"/"+getTime()}).then((res)=>{
                                modalNotificate(res.data);
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.confirmTransferDatas();
                        })
                }
        }
})



// Sales Agent
salesPortal.controller("sales", function($scope,$location,$http, $filter, $timeout,uibDateParser){
        $scope.processingOrder = false;
        $scope.itemAdd = [];  $scope.itemDiscount= 0; $scope.disable = true;
        $scope.salesDate = new Date();
        $scope.dateBanner = $filter('date')(new Date(),'yyyy-MM-dd');
        const customerType = {WALKIN : "WALK-IN", REGULAR : "REGULAR"};

        $scope.showDate = () => $scope.uiOpen = true;

        $scope.fetchQtyType = function(){
                $http({method:"GET",url:"fetchqtytype"}).then(function(res){
                        $scope.selectQtyType = res.data;
                })
        }

        // AUTO RUN 
                fetchItems = function(){
                        $scope.items = [];
                        $http({method:"GET", url:"fetchitems"}).then(function(res){
                                $scope.itemsOrigin = res.data;
                                $scope.items = angular.copy($scope.itemsOrigin);
                        });
                        $scope.fetchQtyType(); 
                }();

                fetchCustomers = function(){
                        $http({method:"GET", url:"fetchcustomers"}).then(function(res){
                                $scope.customers = res.data;
                        });
                }();

                fetchPaymentTypes = function(){
                        $http({method:"GET", url:"fetchpaymenttypes"}).then(function(res){
                                $scope.payments = res.data;
                                $scope.payment = res.data[0];
                        });
                }();

        //
        // window.scope = $scope;

        $scope.pickItem = (index) =>{
                $scope.itemDiscount = 0;
                $scope.itemAmount = null;                
                $scope.itemAmountVal = null; 
                if($scope.customer != null){
                        if($scope.payment != null){
                                $scope.disable = false;
                                $scope.itemName = $scope.items[index].item_name;
                                $scope.itemId = $scope.items[index].item_id;
                                $scope.itemQtyPick();
                        }else{
                                $scope.payValid ="is-invalid";
                                $timeout(()=>{ $scope.payValid=""},2000);
                        }
                }else{
                        modalNotificate("Choose Customer");
                }
        }
        
        $scope.itemQtyPick = () =>{
                if($scope.itemId == null){
                        return ;
                }
                $scope.storeQty = null;
                $scope.itemPrice = null;
                $scope.itemPriceVal = null;
                $scope.costPriceVal = null;
                $scope.sellQty = null;
                $scope.tieredPriceId = null;
                $scope.walkinPriceVal = null;
                if($scope.selectQty){
                        return $http({method:"GET",url:"fetchqtydata/"+$scope.itemId+"/"+$scope.selectQty.qty_id}).then(function(res){
                            $scope.storeQty = $filter('number')(res.data.quantity);

                            if( ($scope.customer.cus_type === customerType.WALKIN) && (res.data.price.walkin_price > 0) ){
                                $scope.itemPrice = $filter('currency')(res.data.price.walkin_price,'',0);
                                $scope.itemPriceVal = angular.copy(res.data.price.walkin_price);
                            }else{
                                $scope.itemPrice = $filter('currency')(res.data.price.max_price,'',0);
                                $scope.itemPriceVal = angular.copy(res.data.price.max_price);
                            }                         

                            $scope.costPriceVal = angular.copy(res.data.price.price);
                            $scope.tieredPrices = res.data.tieredPrice;
                            $scope.walkinPriceVal = angular.copy(res.data.price.walkin_price);
                            if(res.data){
                                    $scope.discountItem = _.find($scope.discountItems, (x)=>{ return (x.item_id == $scope.itemId && x.qty_id == $scope.selectQty.qty_id)});
                            }
                        })
                }
                $scope.sellQty = null;                
                $scope.itemAmount = null;                
                $scope.itemAmountVal = null;                
        }

        $scope.searchItem = (searchData) =>{
                var datas = angular.copy($scope.itemsOrigin);
                if(searchData == null){
                        $scope.items = [];
                        $scope.items = angular.copy($scope.itemsOrigin);
                        return ;
                }
                 searchedData = datas.filter((each)=>{
                        if(each.item_name.toLowerCase().search(searchData.toLowerCase()) >= 0){
                                return each;
                        }
                });
                $scope.items = searchedData;
        }

        $scope.editPickedItem = (index) =>{
                $scope.itemName = $scope.itemAdd[index].name;
                $scope.itemId = $scope.itemAdd[index].itemId;
                $scope.itemQtyPick().then((res)=>{
                        $scope.sellQty = parseFloat($scope.itemAdd[index].qty);
                        $scope.tieredPriceFn();
                });
        }

        $scope.amountFn = () =>{
                $scope.itemAmount = $filter('currency')($scope.itemPriceVal * $scope.sellQty,'',0);
                $scope.itemAmountVal = $scope.itemPriceVal * $scope.sellQty;
                if($scope.discountItem && $scope.sellQty >= 1){
                        $scope.itemDiscount = ($scope.sellQty * $scope.discountItem.discount_amount) / $scope.discountItem.item_qty;
                }else{
                        $scope.itemDiscount = 0;
                }
        }

        $scope.addItem = () =>{
            var qtyTypeDesc = $scope.selectQty.qty_desc;
            var itemPicked = {name:$scope.itemName, unit:qtyTypeDesc, price:$scope.itemPrice, amount: $scope.itemAmount, discount:$scope.itemDiscount, itemId:$scope.itemId, qty:$scope.sellQty, costPrice: $scope.costPriceVal, priceVal:$scope.itemPriceVal, amountVal:$scope.itemAmountVal, tieredPriceId:$scope.tieredPriceId };
            var index = _.findIndex($scope.itemAdd, (each)=>{
                return (each.name == itemPicked.name && each.itemId == itemPicked.itemId)
            });
            
            if( !($scope.storeQty <= 0 || $scope.storeQty ==null || $scope.itemPriceVal <=0 || $scope.itemPriceVal == null || $scope.sellQty <= 0 || $scope.sellQty == null || $scope.sellQty > $scope.storeQty || $scope.itemAmount == null || $scope.itemAmountVal <= 0 || $scope.payment == null) ){
                if(index < 0){
                        $scope.itemAdd.push(itemPicked);
                }else{
                        $scope.itemAdd.splice(index,1,itemPicked);
                }
            }
            $scope.calAmount_discount();
            $scope.sellQty = null;
            $scope.itemAmount = null;                
            $scope.itemAmountVal = null;   
        }

        $scope.calAmount_discount = () =>{
                var total = 0; var discount = 0; var totalQty = 0;
                for(count=0; count < $scope.itemAdd.length; count++){
                        total += $scope.itemAdd[count].amountVal;
                        discount += $scope.itemAdd[count].discount;
                        totalQty += $scope.itemAdd[count].qty;
                }
                $scope.totalQtyVal = totalQty;
                $scope.totalAmountVal = total;
                $scope.totalDiscountVal = discount;
                $scope.totalAmount = $filter('currency')(total,'',0);
                $scope.totalDiscount = $filter('currency')(discount,'',0);
        }

        $scope.removeItem = (index) =>{
                $scope.itemAdd.splice(index,1);
                $scope.calAmount_discount();
        }

        $scope.clearItems = () =>{
                var flag = confirm("Do you want to Clear Items?");
                if(flag){
                        $scope.itemAdd = [];
                        $scope.totalAmount = null;
                        $scope.totalAmountVal = null;
                }
        }

        $scope.pickCustomer = () =>{
                $scope.sellQty = null;
                $scope.storeQty = null;
                $scope.itemPrice = null;
                $scope.itemPriceVal = null;
                $scope.itemAmount = null;
                $scope.itemAmountVal = null;
                $scope.tieredPriceId = null;
                $scope.itemDiscount = 0;
                $scope.credit = '';
                $scope.creditVal ='';
                $http({url:"fetchdiscount", method:"POST", data:$scope.customer}).then((res)=>{    // DISCOUNT ITEMS FETCH
                        $scope.discountItems = res.data;
                }, (res)=>{console.log(res.data)});
                $scope.creditVal = $scope.customer.credit.available_credit;
                $scope.credit = $filter('currency')($scope.customer.credit.available_credit, '', 0);
                $scope.payment = _.find($scope.payments, (d)=> {return d.payment_id == $scope.customer.payment_id}  );
        }

        $scope.saveTransaction = () =>{
                if($scope.itemAdd.length >=1){
                        var flag = confirm("Do you want to Save Transaction?")
                        if(flag){
                                if($scope.payment.payment_desc == 'CREDIT' && $scope.creditVal < $scope.totalAmountVal){
                                        $scope.creditValid ="is-invalid";
                                        $timeout(()=>{ $scope.creditValid=""},2000);
                                }else{
                                        $scope.processingOrder = true;
                                        var subData= {orderItems : $scope.itemAdd, cus_name : $scope.customer.cus_name, cusType : $scope.customer.cus_type, totalDiscount : $scope.totalDiscountVal, order :{
                                        cus_id : $scope.customer.cus_id,
                                        payment_id : $scope.payment.payment_id,
                                        qty_id : $scope.selectQty.qty_id,
                                        cash_paid : $scope.cashPaid,
                                        order_total_qty : $scope.totalQtyVal,
                                        receipt_printed : 1,
                                        order_time : getTime()   } };
                                        if(qz.websocket.isActive()){
                                                modalProcessNotify("<i class='fal fa-cog fa-spin'></i> Processing...");
                                                $scope.ajaxSave(subData);
                                        }else{
                                                modalProcessNotify("<i class='fal fa-cog fa-spin'></i> Processing...");
                                                qz.websocket.connect().then(()=> {
                                                        $scope.ajaxSave(subData);
                                                }).catch(function(e) { console.error(e);
                                                        if(confirm("Print Service not Connected, Do you want to Continue?")){
                                                                $scope.ajaxSave(subData,false);
                                                        }else{
                                                                modalProcessNotify("",'false');
                                                                $scope.processingOrder = false;
                                                                $scope.$digest();
                                                        }
                                                });
                                        }
                                }
                        }else{
                                $scope.processingOrder = false;
                        }
                }
        }

        $scope.ajaxSave = (datas, print=true) =>{
                $http({url:"processorder", method:"POST", data:datas}).then((res)=>{
                        modalProcessNotify("",'false');
                        if(print){
                                qz.printers.getDefault().then((printer)=>{
                                        var config = qz.configs.create(printer);       // Create a default config for the found printer
                                        res.data.forEach((value, index)=>{
                                            qz.print(config, value);
                                        })
                                        modalNotificate("Transaction Successful",2000,'./');
                                })
                        }else{
                                modalNotificate("Transaction Successful",2000,'./');
                        }
                }, (res)=>{
                        modalProcessNotify("",'false');
                        modalNotificate("Transaction Failed",2000,'./');
                })
        }

        $scope.printModule = (data)=>{
                qz.printers.getDefault().then((printer)=>{
                        var config = qz.configs.create(printer);       // Create a default config for the found printer
                        data.forEach((value, index)=>{
                            qz.print(config, value);
                        })
                        modalNotificate("Printing...",2000,'./');
                })
        }

        $scope.reprintReceipt = (orderId)=>{
                modalProcessNotify("<i class='fal fa-cog fa-spin'></i> Processing...");
                $http({url:"reprintreceipt/"+orderId, method:"GET"}).then((res)=>{
                        modalProcessNotify("",'false');
                        if(qz.websocket.isActive()){
                                $scope.printModule(res.data);
                        }else{
                                qz.websocket.connect().then(()=> {
                                        $scope.printModule(res.data);
                                }).catch((e)=>{
                                        console.error(e);
                                        modalProcessNotify("",'false');
                                        modalProcessNotify("Print Service not Connected", 2000, './');
                                })
                        }
                }, (res)=>{console.log(res.data)});

        }

        $scope.salesHistory = () =>{
                var dateData = $filter('date')($scope.salesDate, 'yyyy-MM-dd');
                $http({url:'saleshistory/'+dateData, method:'GET'}).then((res)=>{
                        $scope.dateBanner = dateData;
                        var sum =0;
                        res.data.forEach(ele => {
                                sum+= parseFloat(ele.order_total_amount);
                        });
                        $scope.totalAmountDaily = sum;
                        $scope.historyData = res.data;
                }, (res)=>{console.log(res.data)})
        }     
        
        $scope.tieredPriceFn = () =>{
            $scope.tieredPriceId = null;
            if( ($scope.customer.cus_type != customerType.WALKIN) && ($scope.tieredPrices.length != 0) ){
                    var selectedPrice;
                    var sortedPrice = angular.copy($scope.tieredPrices);
                    var maxQty = Math.max.apply(Math, sortedPrice.map(value =>  value.qty));
                    if($scope.sellQty >= maxQty){
                        selectedPrice = sortedPrice[sortedPrice.length-1].price;
                        $scope.tieredPriceId = sortedPrice[sortedPrice.length-1].id;
                    }else{
                        for(each of sortedPrice){
                            if($scope.sellQty <= each.qty ){
                                selectedPrice =  each.price;
                                $scope.tieredPriceId = each.id;
                                break;
                            }
                        }
                    }
                    if(selectedPrice > 0){
                            $scope.itemPrice = $filter('currency')(selectedPrice,'',0);
                            $scope.itemPriceVal = angular.copy(selectedPrice);
                    }else{
                            $scope.tieredPriceId = null;
                    }
            }            
            $scope.amountFn();
        }
});


/// Stock Keeper
retailOffice.controller("stockout", function($scope,$http, $interval,$sce){
        $scope.stockOutItems = [];
        $scope.confirmStock = [];

        $scope.stockout = function(){
              return  $http({url:"itemsstockout",method:"GET"}).then((res)=>{
                        $scope.stockOutItems = res.data;
                }, (res)=>{console.log(res.data)})
        };
        $scope.stockout();

        $scope.reloadStock = function(){
                        $scope.spinStock = $sce.trustAsHtml("<i class='far fa-sync fa-spin'></i>");
                $scope.stockout().then((res)=>{
                        $scope.spinStock = $sce.trustAsHtml("<i class='far fa-sync'></i>");
                })
        }
        
        $scope.tickItem = function(item){
                        let data = {orderId:item.order_id, confirmTime:getTime(), orderNo:item.order_no};
                if(item.itemChecked){
                        $scope.confirmStock.push(data);
                }else{
                        var index = _.findIndex($scope.confirmStock, (o)=>{return o.orderId == data.orderId});
                        $scope.confirmStock.splice(index,1);
                }
        }

        $scope.confirmStockOut = function(){
                $http({url:"confirmstockout", method:"POST", data: $scope.confirmStock}).then((res)=>{
                        $scope.confirmStock = [];
                        modalNotificate("Stock Out Successful");
                        $scope.stockout();
                }, (res)=>{ console.log(res.data); 
                        modalNotificate("Stock Out Failed");
                });
        }

});

retailOffice.controller("confirmPurchase", function($scope,$http){
        $scope.qtyStatus = ["EXCESS","OUTSTANDING"]; 
        $scope.confirmItem = []; 
        $scope.confirmOutstanding = [];

        $scope.fetchpurchase = function(){
                $http({method:"get",url:"fetchpurchase"}).then(function(response){
                       $scope.confirmPurchaseData = response.data;
                });
        }

        $scope.tickpurchase = function(x){
                var pushFlag = true;
                if(x.isChecked){
                        if(this.outExcessInput==null || this.selOutExcess == null){
                                x.outExcess = null;
                        }else{
                                x.outExcess = {type:this.selOutExcess,qty:this.outExcessInput};
                        }
                        if(this.selOutExcess =="OUTSTANDING" && (this.outExcessInput >= x.purchase_qty || this.outExcessInput <=0 )){
                                pushFlag = false;
                        }else if(this.selOutExcess =="EXCESS" && this.outExcessInput <=0){
                                pushFlag = false;
                        }
                        x.confirmTime = getTime();
                        x.qtyId = $scope.confirmPurchaseData[0].qty_id;
                        if(pushFlag)
                                $scope.confirmItem.push(x);
                }else {
                        var toDel = $scope.confirmItem.indexOf(x);
                        $scope.confirmItem.splice(toDel,1);
                }
        }
        
        $scope.confirmPurchaseBtn = function(){
                if(confirm("Do you want to Confirm Purchase?")){
                        $http({url:"increasestock", data:$scope.confirmItem, method:"POST"}).then(function(response){
                                $scope.confirmItem = []; 
                                modalNotificate('Stock Level Increased',time=2000);
                                $scope.fetchpurchase();
                        }, function(err){
                                modalNotificate('Confirm Purchase Failed!', time=2000);
                        })
                }
        }

        $scope.fetchOutstanding  = function(){
                $http({method:"get",url:"fetchoutstanding"}).then(function(response){
                        $scope.outstandingPurchaseData = response.data;
                 });
        }

        $scope.tickOutstanding = function(x,y){
                var pushFlag = true;
                if(x.isChecked){
                        if($scope.confirmQty <=0 || this.confirmQty == null || this.confirmQty > x.qty){
                                pushFlag = false;
                        }
                        x.confirmQty = this.confirmQty;
                        x.qtyId = y.qty_id;
                        x.confirmTime = getTime();
                        if(pushFlag)
                                $scope.confirmOutstanding.push(x);
                }else{
                        var toDel = $scope.confirmOutstanding.indexOf(x);
                        $scope.confirmOutstanding.splice(toDel,1);
                }
        }

        $scope.confirmOutstandingBtn = function(){
                if(confirm("Do you want to Confirm Purchase Outstandings ?")){
                        $http({url:"confirmoutstanding", data:$scope.confirmOutstanding, method:"POST"}).then(function(response){
                                $scope.confirmOutstanding = []; 
                                modalNotificate('Stock Level Increased',time=2000);
                                $scope.fetchOutstanding();
                        }, function(err){
                                modalNotificate('Confirm Purchase Outstandings Failed!', time=2000);
                        })
                }
        }

        $scope.selDelType = function(){
                this.outExcessInput = "";
        }

})




/// Manager 
retailOffice.controller("purchase", ['$scope','$log','$http', function($scope,$log,$http){

        $scope.purchaseItem = [];
        var sup={};
        $scope.fetchPurchaseDatas = function(){
                let getList = function(data, arrayKey, objKey) {
                      data[arrayKey].forEach(function(element) {
                           element[objKey] = data[objKey]
                      }); 
                    return data[arrayKey];
                };
                $http.get("purchasedata").then(function(response){
                        var itemCat = [];
                        $scope.suppliers = response.data[0];  
                        $scope.paymentTypes = response.data[1];
                        var itemCategories = response.data[2];
                        $scope.qtyTypes = response.data[3];
                         for(x in itemCategories){
                                  itemCat = itemCat.concat(getList(itemCategories[x],"items","cat_desc"));
                         }                                                                               
                        $scope.itemCategory = itemCat;
                })
        };        

        $scope.addItem = function(){
                $scope.amount = 0; 
                var itemAmount = ($scope.item_price * $scope.item_quantity);
                var obj  = {itemId:$scope.item.item_id, 
                                name:$scope.item.item_name, 
                                qty:$scope.item_quantity,
                                price:$scope.item_price,
                                payment:$scope.payment_method.payment_desc,                                
                                itemAmount:itemAmount
                           };
                 
                 sup['supplier']= $scope.supplier.sup_company_name;
                 sup['supplierId']= $scope.supplier.sup_id;
                 sup['paymentId']= $scope.payment_method.payment_id;                                
                 sup['qtyId']= $scope.quantity_type.qty_id;                                
                 sup['purchaseNote']= $scope.purchase_note;
                 var loc = $scope.purchaseItem.map(function(e){ return e.supplier}).indexOf(sup['supplier']);
                 
                if (loc == -1){
                        sup['data']=[];
                        sup.data.push(obj);                        
                        $scope.purchaseItem.push(sup);
                        $scope.item_quantity = "";
                        $scope.item_price = "";
                }else{
                        if($scope.purchaseItem[0].data.find(ele => ele.name === $scope.item.item_name)){
                                modalNotificate("Item Already Added!");
                        }else{
                                $scope.purchaseItem[0].data.push(obj);
                                $scope.item_quantity = "";
                                $scope.item_price = "";
                        }
                }
                        $scope.calAmount($scope.purchaseItem);
        };

        $scope.delItem = function(parent,child){
                $scope.amount = 0;
                if( ($scope.purchaseItem[parent].data.length==1) ){
                        $scope.purchaseItem.splice(parent,1);
                }else{
                        $scope.purchaseItem[parent].data.splice(child,1);
                }   
                $scope.calAmount($scope.purchaseItem);
        };

        $scope.calAmount = function(param){
                $.each(param, function(index,value){
                                $.each(value.data, function(index,nodeV){
                                                $scope.amount = $scope.amount + nodeV.itemAmount;
                                })
                        })
        }

        $scope.cancle = function(){
                if(confirm("Do you want to Cancle Items?"))
                        $scope.purchaseItem = [];
        };

        $scope.savePurchase = function(){
               var config = {url:"savePurchase", method:"POST", data: $scope.purchaseItem};
                if($scope.purchaseItem.length>=1 && confirm("Do you want to Save Purchase?")){
                        $scope.purchaseItem[0].purchaseTime = getTime();
                        $http(config).then(function(response){
                                     modalNotificate("Purchase Successful!");
                                     $scope.purchaseItem = [];
                                     sup['data']=[];
                         }, function(response){
                                     modalNotificate("Purchase Failed !");
                        })
                }
        }

}])

retailOffice.controller("itemPriceCtrl", ['$scope','$log','$http','$filter',"fns", function($scope,$log,$http,$filter,fns){
        $scope.hideCard = true; $scope.hidePrice = true;

        $scope.fetchItemData = function(){
                $http({method:"GET", url:"fetchitemsdata"}).then(function(res){
                        $scope.itemData = res.data;
                })
        }

        $scope.showPrice = function(parentIndex,index){
                $scope.hideCard = false;                
                $scope.pIndex=parentIndex; $scope.cIndex=index;   // Parent and child index for $scope.itemData
                $scope.itemId = $scope.itemData[parentIndex].items[index].item_id;
                $scope.itemName = $scope.itemData[parentIndex].items[index].item_name;
                $scope.qtyTypeSelect();
        }

        $scope.fetchPrice = function(){
                $http({method:"GET",url:"fetchprice/"+$scope.itemId+"/"+$scope.qtyType}).then(function(res){
                        $scope.hidePrice=false;
                        var itemP = res.data;
                        $scope.qtyName = itemP.qty.qty_type.qty_desc;
                        $scope.qty = itemP.qty.quantity;
                        $scope.salePrice = $filter('number')(itemP.sPrice.price);
                        $scope.minPrice = $filter('number')(itemP.sPrice.min_price);
                        $scope.maxPrice = $filter('number')(itemP.sPrice.max_price);
                        $scope.costPrice = itemP.purPrice.purchase_price;
                        $scope.totalPrice = (itemP.purPrice.purchase_price * itemP.qty.quantity);
                        $scope.revenue = (itemP.sPrice.price * itemP.qty.quantity);
                        $scope.profit = $scope.revenue - $scope.totalPrice;
                })
        }

        $scope.qtyTypeSelect = function(){
                if($scope.qtyType){
                        $scope.hidePrice = false;
                        $scope.fetchPrice();
                }else{
                        $scope.minPrice = "";
                        $scope.salePrice = ""; 
                        $scope.maxPrice = "";
                }
        }

        $scope.updatePrice = function(){
                $scope.minPrice = (num => num.split(',').join(''))($scope.minPrice);
                $scope.salePrice = (num => num.split(',').join(''))($scope.salePrice);
                $scope.maxPrice = (num => num.split(',').join(''))($scope.maxPrice);
                var prices = {minPrice:$scope.minPrice, salePrice:$scope.salePrice, maxPrice:$scope.maxPrice, itemId:$scope.itemId, qtyType:$scope.qtyType, time:getTime()};
                if(($scope.minPrice=='') || ($scope.salePrice=='') || ($scope.maxPrice=='') ){
                        modalNotificate('Provide Prices');
                }else{
                        if(confirm("Do you want to Update Price?"))
                                $http({method:"POST",url:"updateprice",data:prices}).then(function(res){
                                        modalNotificate(res.data);
                                        $scope.fetchPrice(); 
                                        $scope.fetchItemData();                                
                                }, function(res){
                                        modalNotificate("Price Update Failed");
                                })    
                }                     
        }

        $scope.fetchConvertData = function(){
                var itemCat =[];
                $http({method:"GET",url:"fetchconvertdata"}).then(function(res){
                        var itemData = res.data['items'];
                        $scope.qtyData = res.data['qtyTypes'];
                        for(x in itemData){
                                itemCat = itemCat.concat(fns.getList(itemData[x],"items","cat_desc"));
                       }                                                                               
                      $scope.itemsConv = itemCat;
                },function(res){ console.log(res.data)});
                $scope.fetchQtyCnv();
        }

        $scope.selQtyCnv = function(x){
                var cnvD =[];
                if(x){
                        var index = $scope.qtyData.indexOf(x);
                        cnvD = angular.copy($scope.qtyData);
                        cnvD.splice(index,1);
                        $scope.qtyCnvData = angular.copy(cnvD);
                }else{
                        $scope.cnvQty = null;
                        $scope.cnvNum = null;
                }
        }

        $scope.chgInput = function(x){
                if(!x){
                        $scope.cnvQty = null;
                        $scope.cnvNum = null;
                }
        }

        $scope.addConvertData = function(){
               var cnvSend = {item_id:$scope.itemCnv.item_id, initial_qty_id:$scope.srcQty.qty_id, initial_qty:$scope.srcNum, converted_qty_id:$scope.cnvQty.qty_id, converted_qty:$scope.cnvNum };
               $http({url:"saveqtyconversion", method:"POST", data:cnvSend}).then(function(res){
                        modalNotificate(res.data.response,time=2000);
                        $scope.fetchQtyCnv();
               },function(res){console.log(res.data)});
        }

        $scope.fetchQtyCnv = function(){
                $http({method:"GET",url:"fecthqtyconversion"}).then(function(res){
                         $scope.cnvDatas = res.data;
                },function(res){console.log(res.data)})
        }

        $scope.delCnv = function(x){
                if(confirm("Do you want to Delete?")){
                        $http({method:"POST",url:"delqtyconversion",data:x}).then(function(res){
                                modalNotificate('Deleted Successfully!', time=2000);
                                $scope.fetchQtyCnv();
                        },function(res){
                                modalNotificate('Delete Failed!', time=2000);
                         })
                }
        }
}])

retailOffice.controller('dashboard', function($scope,$http, $interval,$filter,$location){
        $scope.todayDate = new Date();
        var dateD = $filter('date')(new Date(),'yyyy-MM-dd');

        $scope.fetchQtyType = function(){
                return $http({method:"GET", url:"qtytypes"}).then((res)=>{
                        $scope.qtyTypes = res.data;
                        $scope.qtyTypeP = res.data[0];
                },(res)=>{console.log(res.data)});
        }
        $scope.fetchQtyType().then((res)=>{
                $scope.fetchQtyChart();
        });

        $scope.dashboardData = function(){
                $http({method:'GET', url:'dashboarddata/'+dateD}).then((res)=>{
                        $scope.d = res.data;
                }, (res)=>{console.log(res.data)});
        };
        $scope.dashboardData();

        $interval(()=>{   // INTERVAl EXEC
                $scope.dashboardData();
                // $scope.fetchQty();
        }, 5000);

        $scope.users = function(){
                $http({method:"GET", url:'users'}).then((res)=>{
                        $scope.usersCount = res.data;
                },(res)=>{console.log(res.data)})
        }
        $scope.users();

        $scope.quantityChart = function(labels,datas,bgcolor,bordercolor){
                var ctx = document.getElementById("qtyChart").getContext('2d');
                $scope.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Items Quantity',
                            data: datas,
                            borderWidth: 0.5,
                            backgroundColor: bgcolor,
                            borderColor: bordercolor,
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true,
                                    fontSize: 10
                                }
                            }],
                            xAxes: [{
                                ticks:{
                                        fontSize:10
                                }
                            }]
                        }
                    }
                });
        }

        $scope.fetchQtyChart = function(){
                $scope.qtyChartLoad = false;
                $http({method:"GET", url:"fetchdashqty/"+$scope.qtyTypeP.qty_id}).then((res)=>{
                        var color = res.data.item.map((i)=>{
                                var c =  "rgba(" + Math.floor(Math.random() * 255) + "," + Math.floor(Math.random() * 255) + "," + Math.floor(Math.random() * 255) + ",";
                                var bgcolor = c+"0.2)";
                                var bordercolor = c+"1)";
                                return {bgcolor: bgcolor, bordercolor:bordercolor};
                        });
                        var bgcolor = color.map((j)=>{
                                return j.bgcolor;
                        });
                        var bordercolor = color.map((k)=>{
                                return k.bordercolor;
                        })
                        $scope.qtyChartLoad = true;
                        $scope.quantityChart(res.data.item, res.data.qty, bgcolor, bordercolor);
                }, (res)=>{console.log(res.data)})
        }

        $scope.qtyChange = function(){
                $scope.myChart.destroy();
                $scope.fetchQtyChart();
        }

})

retailOffice.controller('manageUsers', function($scope, $http, fns){
         fns.userRoles().then((res)=>{
                $scope.userRoles = res.data;
        });
         
        $scope.users = function(){
                $http({method:"GET", url:"fetchusers"}).then((res)=>{
                        $scope.usersData = res.data;
                },(res)=>{console.log(res.data)});
        }
        $scope.users();

        $scope.userStatus = function(userId){
                $http({method:"GET", url:"userstatus/"+userId+"/"+$scope.statusChk}).then((res)=>{
                        $scope.users();
                        $scope.pickedUser.status = res.data;
                },(res)=>{console.log(res.data)});
        }

        $scope.pickUser = function(x){
                $scope.pickedUser = $scope.usersData[x];
                $scope.statusChk = ($scope.pickedUser.status == "ACTIVE") ? true:false;
                $scope.editName = $scope.pickedUser.name;
                $scope.editMail = $scope.pickedUser.email;
                $scope.editPhone = $scope.pickedUser.phone;
                $scope.editID = $scope.pickedUser.username;
                $scope.editPassword = $scope.pickedUser.password;
        }

        $scope.addUser = function(){
                var userdataReg = {'name':$scope.name, 'email':$scope.mail, 'phone':$scope.phone, 'username':$scope.id, 'password':$scope.password, 'role':$scope.role, 'status':$scope.status};
                $http({method:"POST", url:"newuser",data:userdataReg}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.users();
                },(res)=>{console.log(res.data)});
        }

        $scope.editUser = function(){
                var userdataEdit = {'name':$scope.editName, 'email':$scope.editMail, 'phone':$scope.editPhone, 'username':$scope.editID, 'password':$scope.editPassword, 'role':$scope.editRole, 'userId':$scope.pickedUser.user_id};
                $http({method:"POST", url:"edituser",data:userdataEdit}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.users();
                },(res)=>{console.log(res.data)});

        }
})

retailOffice.controller('customers', function($scope, $http, fns){
        $scope.creditTypes = {CREDIT:"CREDIT", DEBIT:"DEBIT"}; var itemCat =[];
        $scope.creditType = $scope.creditTypes.CREDIT;
        fns.payments().then((res)=>{
                $scope.payData = res.data;
        });

        fns.qtyTypes().then((res)=>{
                $scope.disDatas = res.data;
                $scope.disQtyType=$scope.disDatas.qtyTypes[0];
                for(x in res.data.items){
                        itemCat = itemCat.concat(fns.getList(res.data.items[x],"items","cat_desc"));
                }
                $scope.disItemsTypes = itemCat;
        });


        $scope.fetchCustomers = function(){
                return $http({method:"GET", url:"customerdatas"}).then((res)=>{
                        $scope.customersData = res.data;
                        $scope.searched = res.data
                },(res)=>{console.log(res.data)});
        }
        $scope.fetchCustomers();

        $scope.addCustomer = function(){
                var cusdata = {'name':$scope.cusName, 'email':$scope.cusMail, 'phone':$scope.cusPhone, 'address':$scope.cusAddress, 'payment':$scope.cusPay };
                $http({method:"POST", url:"addcustomer",data:cusdata}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.fetchCustomers();
                },(res)=>{console.log(res.data)});
        }

        $scope.pickCus = function(x){
                $scope.cusIndex = x;
                $scope.pickedCus = $scope.searched[x];
                $scope.editcusName = $scope.pickedCus.cus_name;
                $scope.editcusPhone = $scope.pickedCus.cus_mobile;
                $scope.edicustMail = $scope.pickedCus.cus_mail;
                $scope.id = $scope.pickedCus.cus_id;
                $scope.editcusAddress = $scope.pickedCus.cus_address;
                $scope.creditOrder();
                $scope.discountItems();
        }

        $scope.editCustomer = function(){
                var cusdataEdit = {name: $scope.editcusName, phone:$scope.editcusPhone, mail:$scope.edicustMail, pay:$scope.editcusPay, address:$scope.editcusAddress, id:$scope.id};
                $http({method:"POST", url:"editcustomer",data:cusdataEdit}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.fetchCustomers();
                },(res)=>{console.log(res.data)});
        }

        $scope.searchCustomer = function(){
                $scope.searched = fns.search($scope.searchCus, $scope.customersData, 'cus_name');
        }
        
        $scope.creditProcess = function(){
                var data = {type:$scope.creditType, amt:$scope.creditAmt, cus_id:$scope.pickedCus.cus_id, time:getTime()};
                $http({method:"POST", url:"creditprocess", data:data}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.fetchCustomers().then(()=>{$scope.pickCus($scope.cusIndex)});
                },(res)=>{console.log(res.data)});
        }

        $scope.creditOrder = function(){
                var data = {cus_id:$scope.pickedCus.cus_id};
                $http({method:"POST", url:"creditorders",data:data}).then((res)=>{
                        $scope.creditOrders = res.data;
                        $scope.fetchCredit(data.cus_id).then((res)=>{
                                $scope.pickedCus.credit.available_credit = res.available_credit;
                                $scope.pickedCus.credit.out_credit = res.out_credit;
                        })
                },(res)=>{console.log(res.data)});
        }

        $scope.payDebit = function(amt,creditOrderId){
                if(confirm("Do you want to Confirm Payment?")){
                        var data = {cus_id:$scope.pickedCus.cus_id, amount:amt, time:getTime(), creditOrderId:creditOrderId };
                        $http({method:"POST", url:"paydebit",data:data}).then((res)=>{
                                modalNotificate(res.data.response);
                                $scope.creditOrder();
                        },(res)=>{console.log(res.data)});
                }
        }

        $scope.fetchCredit = function(cusId){
                return $http({method:"POST", url:"fetchcredit",data:{cusId:cusId}}).then((res)=>{
                        return res.data;
                },(res)=>{console.log(res.data)});
        }

        $scope.payDiscount = function(){
                var datas = {cusId:$scope.pickedCus.cus_id, amount:$scope.disAmount, time:getTime()};
                if(confirm("Do you want to Pay Discount?")){
                        $http({method:"POST", url:"paydiscount",data:datas}).then((res)=>{
                                modalNotificate(res.data.response);
                                $scope.fetchDiscount(datas.cusId).then((res)=>{
                                        $scope.pickedCus.discount.discount_credit = res.discount_credit;
                                })
                        },(res)=>{console.log(res.data)});
                }
        }

        $scope.fetchDiscount = function(cusId){
                return $http({method:"POST", url:"fetchdiscountbal",data:{cusId:cusId}}).then((res)=>{
                        return res.data;
                },(res)=>{console.log(res.data)});
        }

        $scope.addDiscount = function(){
                var datas = {unit:$scope.disQtyType, qty:$scope.discountQty, item:$scope.disItem, amount:$scope.discountAmt, cusId:$scope.pickedCus.cus_id, time:getTime()};
                        $http({method:"POST", url:"adddiscount",data:datas}).then((res)=>{
                                modalNotificate(res.data.response);
                                $scope.discountItems();
                        },(res)=>{console.log(res.data)});
        }

        $scope.discountItems = function(){
                var data = {cusId:$scope.pickedCus.cus_id};
                $http({method:"POST", url:"discountitems",data:data}).then((res)=>{
                        $scope.fetchedDisItems = res.data;
                },(res)=>{console.log(res.data)});
        }

        $scope.delDiscount = function(x){
                var data = {cusId:$scope.pickedCus.cus_id, item:x.item.item_id, qty:x.unit.qty_id};
                $http({method:"POST", url:"deldiscount",data:data}).then((res)=>{
                        modalNotificate(res.data.response);
                        $scope.discountItems();
                },(res)=>{console.log(res.data)});
        }
})

retailOffice.controller('history', function($scope, $http, $filter){
        $scope.salesDateHis = new Date();

        $scope.showDate = function(){
                $scope.uiOpen = true;
        }

        $scope.salesHistory = function(){
                        var dateData = $filter('date')($scope.salesDateHis, 'yyyy-MM-dd');
               return $http({url:'managersaleshistory/'+dateData, method:'GET'}).then((res)=>{
                        $scope.salesHistoryData = res.data;
                }, (res)=>{console.log(res.data)})
        } 
        $scope.salesHistory();

        $scope.orderItems = function(index){
                $scope.salesIndex = index;
                $scope.orderItemsData = $scope.salesHistoryData[index];
                modalOrderHistory();
        }

        $scope.cancleOrder = function(orderId){
                var conf = confirm('Do you want to Cancle Order?');
                if(conf){
                        $http({method:"GET", url:"canclesalesorder/"+orderId+"/"+getTime()}).then((res)=>{
                                modalNotificate('Order Cancled!');
                                $scope.salesHistory().then((res)=>{
                                        if($scope.orderItemsData)    // Reload Modal Information
                                                $scope.orderItemsData = $scope.salesHistoryData[$scope.salesIndex];
                                });
                        }, (res)=>{console.log(res.data)});
                }
        }

        $scope.transferHistory  = function(){
                var dateData = $filter('date')($scope.salesDateHis, 'yyyy-MM-dd');
                $http({url:'managertransferhistory/'+dateData, method:'GET'}).then((res)=>{
                        $scope.transferHistoryData = res.data;
                }, (res)=>{console.log(res.data)})
        }
        $scope.transferHistory();

        $scope.cancleTransfer = function(transferId){
                if(confirm("Do you want to Cancle Transfer?")){
                        $http({method:"GET", url:"cancletransfer/"+transferId+"/"+getTime()}).then((res)=>{
                                modalNotificate('Transfer Cancled!');
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.transferHistory();
                        })
                }
        }

        $scope.purchaseHistory = function(){
                var dateData = $filter('date')($scope.salesDateHis, 'yyyy-MM-dd');
                $http({url:'managerpurchasehistory/'+dateData, method:'GET'}).then((res)=>{
                        $scope.purchaseHistoryData = res.data;
                }, (res)=>{console.log(res.data)})
        }
        $scope.purchaseHistory();

        $scope.canclePurchase = function(purchaseId,itemId){
                if(confirm("Do you want to Cancle Purchase?")){
                        $http({method:"GET", url:"canclepurchase/"+purchaseId+"/"+itemId+"/"+getTime()}).then((res)=>{
                                modalNotificate('Purchase Cancled!');
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.purchaseHistory();
                        })
                }
        }

        $scope.cancleAllPurchase = function(purchaseId){
                if(confirm("Do you want to Cancle all Purchase?")){
                        $http({method:"GET", url:"cancleallpurchase/"+purchaseId+"/"+getTime()}).then((res)=>{
                                modalNotificate('All Purchase Cancled!');
                        },(res)=>{console.log(res.data)}).then((res)=>{
                                $scope.purchaseHistory();
                        })
                }
        }

})

retailOffice.controller('inventory', function($scope, $http, $filter, $window){

        $scope.fromDate = new Date();

        $scope.toDate = new Date();

        $scope.showLoader = false;

        $scope.itemHistoryList = [];

        $scope.showFromDate = function(){
                $scope.fromUiOpen = true;
        }
        
        $scope.showToDate = function(){
                $scope.toUiOpen = true;
        }

        $scope.invItems = function(){
                $http({method:"GET", url:"inventoryitems"}).then((res)=>{
                        $scope.invLists = res.data;
                }, (res)=>{console.log(res.data)})
        }
        $scope.invItems();

        $scope.selectInvItem = function(itemId){
                $scope.itemId = itemId;
                $http({method:"GET", url:"selectedInv/"+itemId}).then((res)=>{
                        $scope.selectedInv = res.data;
                        $scope.qtyTypeId = $scope.selectedInv['item_qty'][0];

                }, (res)=>{console.log(res.data)}).then((res) => {
                        $scope.itemQtyHistory();
                });
        }

        $scope.removeItem = function(){
                if(!($scope.remQtyType==null || $scope.remQty == null || $scope.remQty <= 0 || $scope.remQty > $scope.remQtyType.quantity)){
                        if(confirm('Do you want to Continue?')){
                                var datas = {qtyId:$scope.remQtyType, qty:$scope.remQty, note:$scope.remNote, time:getTime()};
                                $http({method:"POST", url:"removeitem", data:datas}).then((res)=>{
                                        modalNotificate('Item Quantity Removed.');
                                        $scope.selectInvItem($scope.remQtyType.item_id);
                                }, (res)=>{console.log(res.data)})
                        }
                }
        }

        $scope.itemQtyHistory = () => {

                $scope.showLoader = true;

                var fromDate = $filter('date')($scope.fromDate,"yyyy-MM-dd");
                
                var toDate = $filter('date')($scope.toDate,"yyyy-MM-dd");

                var datas = {fromDate : fromDate, toDate : toDate, qtyId : $scope.qtyTypeId.qty_id, itemId : $scope.itemId  };
                
                $http({method:"POST", url:"itemqtyhistory", data:datas}).then((res) => {
        
                        $scope.showLoader = false;

                        $scope.itemHistoryList = res.data;

                });
        }

        $scope.downloadQtyHistory = ()=>{
                
                var fromDate = $filter('date')($scope.fromDate,"yyyy-MM-dd");
                
                var toDate = $filter('date')($scope.toDate,"yyyy-MM-dd");
                
                var qtyId = $scope.qtyTypeId.qty_id;

                var itemId = $scope.itemId;
                
                var landingUrl = "downloaditemqtyhistory/"+fromDate+"/"+toDate+"/"+qtyId+"/"+itemId;
                
                $window.location.href = landingUrl;    

        }

})

retailOffice.controller('report', function($scope, $http, $filter, $window, fns){
        $scope.showLoader = true;
        $('.loaders').scheletrone();
        $scope.fromDate = new Date();
        $scope.toDate = new Date();
        $scope.qtyTypeList = [];
        $scope.reportMenu = [
                {value:1, type:"Top Sales Stocks"},
                {value:2, type:"Sale Items Report"},
                {value:3, type:"Sales Report"},
                {value:4, type:"Sale Items Summary Group By Unit Price"},
                {value:5, type:"Daily Sales Summary Report"},
                {value:6, type:"Sale Items Summary"},
                {value:7, type:"All items"},
                {value:8, type:"Cancled Items"},
                {value:9, type:"Purchased Items"},
                {value:10, type:"Total Summary Report"},
                {value:11, type:"Top Customers by Ticket"},
                {value:12, type:"Top Customers by Amount"},
                {value:13, type:"Top Customers by Volume"},
        ];
        
        $scope.selectedReport  = $scope.reportMenu[0];

        $scope.showFromDate = function(){
                $scope.fromUiOpen = true;
        }
        
        $scope.showToDate = function(){
                $scope.toUiOpen = true;
        }

        fns.qtyTypes().then(res => {
            $scope.qtyTypeList = res.data.qtyTypes
            $scope.qtyTypeId = $scope.qtyTypeList[1];
        }).then(res => {

            $scope.salesReport = function(){
                $scope.showLoader = true;
                var fromDate = $filter('date')($scope.fromDate,"yyyy-MM-dd");
                var toDate = $filter('date')($scope.toDate,"yyyy-MM-dd");
                var datas = {fromDate: fromDate, toDate: toDate, reportType : $scope.selectedReport.value, qtyId: $scope.qtyTypeId.qty_id };
                $http({method:"POST", url:"salesreport", data:datas}).then((res)=>{
                    $scope.showLoader = false;
                    switch ($scope.selectedReport.value) {
                        case 1:
                                $scope.topSales = res.data.topSales;
                                $scope.totalSummary = res.data.summary;
                                $scope.grandTotal = res.data.summary.reduce((acc, each) => {return acc + each.totalAmount}, 0);
                            
                            break;
                        case 2:
                                $scope.saleItems = res.data.saleItems;
                                $scope.saleItemsTotals = res.data.totals;
                            
                            break;
                        case 3:
                                $scope.sales = res.data.sales;
                                $scope.saleItemsTotals = res.data.totals;
                            
                            break;
                        case 4:
                                $scope.salesItemsByUnitPrice = res.data.salesItemsByUnitPrice;
                                $scope.saleItemsTotals = res.data.totals;
                            
                            break;
                        case 5:
                                $scope.dailySalesSummary = res.data.dailySalesSummary;
                                $scope.saleItemsTotals = res.data.totals;
                            
                            break;
                        case 6:
                                $scope.saleItemsSummary = res.data.saleItemsSummary;
                                $scope.saleItemsTotals = res.data.totals;
                            
                            break;
                        case 7:
                                $scope.allItems = res.data.allItems;
                                $scope.allItemsTotals = {totalQty : res.data.totalQty};
                            
                            break;
                        case 8:
                                $scope.cancledItems = res.data.cancledItems;
                                $scope.cancledItemsTotals = {totalQty : res.data.totalQty, totalAmount : res.data.totalAmount};
                            
                            break;
                        case 9:
                                $scope.purchasedItems = res.data.purchasedItems;
                                $scope.purchasedItemsTotals = {totalQty : res.data.totalQty, totalAmount : res.data.totalAmount};
                            
                            break;
                        case 10:
                                $scope.totalDailySummary = res.data.stores;
                                $scope.dailySummaryGrandTotal = res.data.grandTotal;
                            break;
                        case 11:
                                $scope.cusByTicketList = res.data.cusByTicketList;
                                $scope.cusByTicketTotals = {totalTicket: res.data.totalTicket, totalQty : res.data.totalQty, totalAmount : res.data.totalAmount}
                                break;
                        case 12:
                                $scope.cusByAmountList = res.data.cusByAmountList;
                                $scope.cusByAmountTotals = {totalQty : res.data.totalQty, totalAmount : res.data.totalAmount}
                                break;
                        case 13:
                                $scope.cusByVolumeList = res.data.cusByVolumeList
                                $scope.cusByVolumeTotals = {totalQty : res.data.totalQty, totalAmount : res.data.totalAmount}
                                break;
                        default:
                            break;
                    }

                }, (res)=>{console.log(res.data)})
            }

            $scope.salesReport();

        })

       

        $scope.downloadReport = function(){
            var fromDate = $filter('date')($scope.fromDate,"yyyy-MM-dd");
            var toDate = $filter('date')($scope.toDate,"yyyy-MM-dd");
            var reportType = $scope.selectedReport.value;
            var qtyId = $scope.qtyTypeId.qty_id;
            var landingUrl = "downloadreport/"+fromDate+"/"+toDate+"/"+reportType+"/"+qtyId;
            $window.location.href = landingUrl;       
        }

})

retailOffice.config(function($routeProvider){
        $routeProvider.when('/editprofile',{
                templateUrl:"editprofile"
        }).when('/adduser', {
                templateUrl:"newprofile"
        }).when('/profile', {
                templateUrl:"profile"
        }).when('/cusprofile', {
                templateUrl:"cusprofile"
        }).when('/newcustomer', {
                templateUrl:"newcustomer"
        }).when('/editcustomer', {
                templateUrl:"editcustomer"
        })
})

function getTime(){
        var currentTime = new Date();
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();
                if(minutes<10){
                        minutes='0'+minutes;
                }	
        tm = hours+':'+minutes+':'+seconds;
        return tm;
}