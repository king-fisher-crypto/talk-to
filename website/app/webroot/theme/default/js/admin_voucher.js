var nx_voucher = {

    init: function(){
        this.initProducts();
        this.initCountries();

        nx_voucher.initList();

        //Ajout d'un client
        $('#addCustomer').click(function(){
            //La valeur de l'input
            var customer = $('#list_customer').val();

            nx_voucher.postToServer($(this).attr('href'), customer);
            //on vide l'input
            $('#list_customer').attr('value', '');
        });
    },
    addCustomerInList: function(customer, listCustomer){
        if(jQuery.inArray(customer.personal_code, listCustomer) == -1){
            $('#list_population').append('<li pc="'+customer.personal_code+'">'+customer.personal_code+' - '+customer.firstname+'<i class="icon-remove margin-left"></i></li>');
        }
    },
    initList : function(){
        var inputVal = $('#list_final').val();
        //S'il y a des valeurs
        if(inputVal.length != 0){
            nx_voucher.postToServer($('#addCustomer').attr('href'), inputVal);
        }
    },
    initProducts: function(){
        $("#VoucherAllproducts").click(function(){
            if ($("#VoucherAllproducts").is(":checked")){
                $("#list-of-products .lop-check").hide();
            }else{
                $("#list-of-products .lop-check").show();
            }
        });

    },
    initCountries: function(){
        $("#VoucherAllcountries").click(function(){
            if ($("#VoucherAllcountries").is(":checked")){
                $("#list-of-countries .lop-check").hide();
            }else{
                $("#list-of-countries .lop-check").show();
            }
        });

    },
    postToServer: function(url, customer){
        nxAdminMain.ajaxRequest(url, { customer: customer}, function(json){
            if(json.return === true){
                var listCustomer = nx_voucher.listCustomer();
                //Pour chaque customer
                for(var key in json.customers){
                    nx_voucher.addCustomerInList(json.customers[key], listCustomer);
                }
                nx_voucher.updateInput();
                nx_voucher.initEvent();
            }else{
                if(json.msg !== undefined){
                    alert(json.msg);
                }
            }
        });
    },
    initEvent: function(){
        //Suppression d'un client
        $('#list_population li i').unbind('click').click(function(){
            var pc = $(this).parent('li').attr('pc');
            if(pc != undefined){
                nx_voucher.removeCustomer(pc);
            }
        });
    },
    removeCustomer: function(pc){
        $('li[pc="'+pc+'"]').remove();
        nx_voucher.updateInput();
    },
    updateInput: function(){
        var strCustomer = nx_voucher.listCustomer();
        strCustomer = strCustomer.join(',');
        //MAJ de l'input final
        $('#list_final').attr('value', strCustomer);
    },
    listCustomer: function(){
        var data = [];
        //Pour chaque customer de la liste
        $('#list_population li').each(function(){
            data.push($(this).attr('pc'));
        });

        return data;
    }
}

$(document).ready(function(){ nx_voucher.init(); });