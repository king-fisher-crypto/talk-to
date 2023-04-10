<?php
$logged = false;
if ($this->Session->read('Auth.User')) $logged = true;
?>

<header>

    <div class="btntopmenu navbar-toggle left-toggle offcanvas-toggle" data-toggle="offcanvas" data-target="#offcanvas">
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
    </div>


    <a  href="<?= Configure::read('Site.baseUrlFull') ?>"><div class=" logo center_v" > 
	</div></a> 


    <div class="search center_v">
	<div class="loupe ">  
	</div>
        <form>
            <input type="text" class="center_v" placeholder="<?= __('Chercher') ?>">
        </form>
    </div>

    <DIV class="crediter center_v">
	<a class="btn blue h49"   href="/logout" title="<?= __('Créditer') ?>"><?= __('Créditer') ?> +</a>  
	<span>0,00 $</span>
    </DIV>

    <div class="navbar-myaccount">
	<?php
	if ($userRole == 'agent') echo $this->FrontBlock->getAgentAlertes();
	//if($userRole == 'client')
	//echo $this->FrontBlock->getAccountAlertes(); 

	echo $this->FrontBlock->getHeaderUserBlock();
	?>
    </div>

    <div class="navbar-myaccount-btn <?php if ($this->Session->read('Auth.User')) echo 'connect'; ?> navbar-toggle right-toggle collapse-toggle center_v" data-toggle="collapse" data-target="#offcanvasaccount">
    </div>

    <div class="pictos center_v">
	<div class="loupe "></div>
	<a <?php if (!$logged)
	    { ?>
    	    rel="modal:open" href="#connection" 
	    <?php }
	    else
		{ ?>  
    	    href="/accounts/profil" 
		    <?php } ?> >
	    <div class="user  connect"  ></div></a>
	<div class="messages ">
	    <?php if ($userRole == 'agent')
		{ ?>
    	    <div class="nbre"><div class="disk">
    <?= $this->FrontBlock->getAgentAlertes(); ?>
    		</div></div>
<?php } ?>
	</div>
    </div>

   


</header>
 <div class="search_mobile">
    <div class="search   ">
	<div class="loupe" >  
	</div>
        <form>
            <input class="center_v" type="text" placeholder="<?= __('Chercher') ?>">
        </form>
    </div>
    </div>