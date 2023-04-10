var nx_credit = {
    current: 0,
    target: '.headermenucon .credits span.value',
	 target_menu: '#offcanvasaccount span.value',
    timer:0,
    interval: 8,
    init: function(){

        nx_credit.refresh();
    },
    refresh: function(){
        var target = $(document).find(nx_credit.target);
var target_menu = $(document).find(nx_credit.target_menu);
        nxMain.ajaxRequest('/home/ajaxgetcredit/',{},function(json){
            if (nx_credit.current != json.credit){
				txt_credit_tmp = json.text.split('soit ');
				txt_credit_tmp = txt_credit_tmp[1];
				txt_credit = txt_credit_tmp.replace('</span>','');
                target.html(txt_credit);
				target_menu.html(txt_credit);
                nx_credit.current = json.credit;
            }
            

            nx_credit.timer = setTimeout(function(){
                nx_credit.refresh();
            }, (nx_credit.interval * 1000));
        });
        return false;
    }
}
$(document).ready(function(){nx_credit.init();});