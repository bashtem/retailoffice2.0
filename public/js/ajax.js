$(document).ready(function(){

    $('[data-toggle="tooltip"]').tooltip();
    
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
    });
   
    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
    })

	var timer = setInterval(reload, 60000);
	$(this).mousemove(function(e){
		interval = 0;
    });
})

var interval = 0;

function modalNotificate(msg='',time=2000, url=''){
    $('#modalMsg').html(msg);
        $('#modalNotify').modal('show');
    setTimeout(function(){
        $('#modalNotify').modal('hide');
        if(url!=''){window.location.href=url}
    }, time);
}

function modalOrderHistory(){
    $('.orderHistory').modal('show');
}

function modalProcessNotify(msg='', show='true'){
    $('#modalMsgP').html(msg);
    if(show =='true'){
        $('#modalP').modal({show:true, backdrop:false});
    }else{
        $('#modalP').modal('hide');
    }
}

function reload(){
    interval = interval+1;
    if(interval == 120){
        window.location.assign('./');
    } 
}