<html>
<body>
<style>
span.method_get { border-radius:3px; padding:0px 5px; background-color:#00aeff; color:#fff; font-weight:bold; font-size:10px; }
span.method_post { border-radius:3px; padding:0px 5px; background-color:#2fa73d; color:#fff; font-weight:bold; font-size:10px; }
.leftpart { width:700px; float:left; padding-right:20px; border-right:solid 5px #EEE;}
.rightpart { display: block; margin-left: 700px; padding: 0 40px; width: 500px; position:fixed}
body { font-family:Arial, Helvetica, sans-serif; font-size:11px }
.controller { border:solid 1px #CCC; padding:2px 10px; }
.controller a { display:block; color:#000; font-size:20px; font-weight:bold; text-decoration:none; }
.methods { border:solid 1px #CCC; margin-left:40px; padding:10px; margin: 0 0 10px 80px; border-top:none; }
.methods .method.selected span.method_title a { background-color:#3787a1; color:#FFF; }
.methods .method.selected span.method_title a span.url { color:#FFF; }
.methods span.method_title a { display:block; border:solid 1px #CCC; padding:3px 10px; font-size:14px; color:#666; text-decoration:none; font-weight:bold }
span.url { font-size:11px; color:#666; font-weight:normal }
.parms { background-color:#EEE; padding:15px; margin:0 0 20px 30px }
.parm { margin:2px 0; }
.parm label { float:left; width:250px; text-align:right; margin-right:5px; line-height:20px }
.rightpart h1 { background-color:#EEE; font-size:16px; border:solid 1px #CCC; padding:2px 10px; margin:0} 
.rightpart .responsebox { font-size:12px; border:solid 1px #CCC; padding:20px 10px; margin:0 0 20px 30px} 
.test { background-color:#EEE; border-radius:5px; margin-bottom:10px; padding:10px }
.rko { background-color:#ffcece; color:#FF0000; }
.rok { background-color:#cbffb4; color:#0ba100; }
.method_active { border-right:solid 4px #54a14c }
.method_inactive { border-right:solid 4px #a14c4c }
input[type=text] { border:solid 1px #666; font-size:14px; text-align:center; font-family:arial;  }
</style>
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
var tools = {
    init: function(){
        $("a").click(function(){return false;});
        $(".resultbox").hide();
        $("form").submit(function(){
           $(".resultbox").hide();
           tools.selectMethod('','');
           $(".dyn_response_code").html(' ');
           $(".dyn_response_message").html(' ');
           $(".dyn_response").html(' ');
           $(".dyn_url").html(' ');
           var action = $(this).attr("action");
           var tmp = action.split("/");
           tools.ajaxRequestApi(action, $(this).serialize(), function(json, url){
               $(".dyn_response_code").html(json.response_code);
               $(".dyn_response_message").html(json.response_message);
               $(".dyn_response").html(json.response);
               $(".dyn_url").html(url);
               
               if (json.response_code == '0'){
                   $(".dyn_response_message").addClass("rok").removeClass("rko");
               }else{
                   $(".dyn_response_message").addClass("rko").removeClass("rok");
               }
               
               var parms = '';
               for (i in json.request_parms){
                   parms+= i+' => '+json.request_parms[i]+'<br/>';
               }
               $(".dyn_parms").html(parms);
               tools.selectMethod(tmp['2'],tmp['3']);
               $(".resultbox").fadeIn(200);
           },'json', $(this).attr("method"));
           return false;
        });
    },
    selectMethod: function(controller, method){
        $("div.method").removeClass("selected");
        $("div[id="+controller+"-"+method+"-box]").addClass("selected");
    },
    ajaxRequestApi: function(url, postVars, callback, format, type){
        if (type == 'get'){
            tmp = postVars.split("&");
            var urlsuffix = '';
            for (i in tmp){
                var tmp2 = tmp[i].split("=");
                if (tmp2[1] != '')
                    urlsuffix+= tmp2[1]+'/';
            }
            url+= urlsuffix;
            return this.ajaxRequest(url, {}, callback, format, type);
        }else{
            return this.ajaxRequest(url, postVars, callback, format, type);
        }
    },
    ajaxRequest: function(url, postVars, callback, format, type){
        $.ajax({
            type: type,
            dataType: format,
            url: url,
            data: postVars,
            success: function(datas){
                if (callback != undefined)
                    callback(datas, url);
            }
        });
    }
}
$(document).ready(function(){
    tools.init();
})
</script>


<div class="leftpart">

<?php foreach ($controllers AS $controller => $methods){ 

?>

    <div class="controller"><a href=""><?php echo $controller; ?></a></div>
    <div class="methods">
        <?php foreach ($methods AS $method){

            if (isset($parms['get-'.$controller.'-'.$method]))
                $formMethod = 'get';
            elseif (isset($parms['post-'.$controller.'-'.$method]))
                $formMethod = 'post';
            else $formMethod = false;


            ?>
            <div class="method<?php echo (isset($api_method_ready[$formMethod.'-'.$controller.'-'.$method]) && ($api_method_ready[$formMethod.'-'.$controller.'-'.$method] == true))?' method_active':' method_inactive'; ?>" id="<?php echo $controller.'-'.$method.'-box'; ?>">
                <form action="/api/<?php echo $controller."/".$method."/"; ?>" method="<?php echo $formMethod; ?>">
                    <span class="method_title">
                        <a href="">
                            <span class="method_<?php echo $formMethod; ?>"><?php echo strtoupper($formMethod); ?></span>
                            <?php echo $method; ?> <span class="url">(/api/<?php echo $controller."/".$method."/"; ?>)</span></a></span>
                    <div class="parms">
                        <?php foreach ($parms[$formMethod.'-'.$controller.'-'.$method] AS $parm): ?>
                            <div class="parm">
                                <label for="<?php echo $controller.'-'.$method.'-'.$parm; ?>"><?php echo $parm; ?></label>
                                <input type="text" name="<?php echo $parm; ?>" value="" id="<?php echo $controller.'-'.$method.'-'.$parm; ?>" />

                            </div>
                        <?php endforeach; ?>



                        <?php 
                        if (isset($parms[$controller.'-'.$method])){
                            foreach ($parms[$controller.'-'.$method] AS $parm){
                        ?>
                        <div class="parm">
                            <label for="<?php echo $controller.'-'.$method.'-'.$parm; ?>"><?php echo $parm; ?></label>
                            <input type="text" name="<?php echo $parm; ?>" value="" id="<?php echo $controller.'-'.$method.'-'.$parm; ?>" />
                        </div>                    
                        <?php
                            }
                        }
                        ?>
                        <input type="submit" value="Envoyer" />
                    </div>
                </form>
            </div>
            
        <?php } ?>
    </div>
<?php } ?>
</div>
<div class="rightpart">
    <div class="test"><strong>valeurs de test:</strong><br/> Cust_personal_code = 1999 <br/>agent_number = 46987</div>
    <h1>Paramètres envoyés :</h1>
    <div class="dyn_parms responsebox"></div>
    <p>Résultat :</p>
    <div class="resultbox">
        <h1>Url appelée :</h1>
        <div class="dyn_url responsebox"></div>
        <h1>Response Code :</h1>
        <div class="dyn_response_code responsebox"></div>
        <h1>Response Message :</h1>
        <div class="dyn_response_message responsebox"></div>
        
        <h1>Response : </h1>
        <div class="dyn_response responsebox"></div>
    </div>
</div>
<div style="clear:both"></div>
</body>
</html>