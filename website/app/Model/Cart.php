<?php
App::uses('AppModel', 'Model');
/**
 * CustomerCredit Model
 *
 * @property Customer $Customer
 */
class Cart extends AppModel {
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'type'  => 'left',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProductLang' => array(
            'className' => 'ProductLang',
            'foreignKey' => '',
            'conditions' => array('Cart.product_id = ProductLang.product_id', 'ProductLang.lang_id = Cart.lang_id'),
            'fields' => '',
            'order' => ''
        ),
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => '',
            'conditions' => array('Cart.country_id = Country.id'),
            'fields' => '',
            'order' => ''
        ),
        'Voucher' => array(
            'className' => 'Voucher',
            'foreignKey' => '',
            'conditions' => array('Cart.voucher_code = Voucher.code', 'Voucher.active = 1'),
            'fields' => array('credit','amount','percent','code','title','buy_only'),
            'order' => ''
        ),
		'GiftOrder' => array(
            'className' => 'GiftOrder',
            'foreignKey' => '',
            'conditions' => array('Cart.voucher_code = GiftOrder.code', 'GiftOrder.valid >= 1', 'GiftOrder.valid <= 2'),
            'fields' => array('amount','code','devise'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => '',
            'conditions' => array('Cart.user_id = User.id'),
            'fields' => array('firstname','lastname','id','email','careers'),
            'order' => ''
        ),
        'Lang' => array(
            'className' => 'Lang',
            'foreignKey' => '',
            'conditions' => array('Cart.lang_id = Lang.id_lang'),
            'fields' => array(),
            'order' => ''
        )
    );
    public function getDatas($id_cart=0)
    {
        if (!$id_cart)return false;

        $reduction_mode = false;
        $reduction_amount = 0;

        $datas = $this->find("first", array(
            'conditions' => array('Cart.id' => $id_cart),
            'fields'     => array(),
            'recursive'  => 1
        ));

        $credits = $datas['Product']['credits'];
        $credits_save = $datas['Product']['credits'];
		$vouch = false;
        if(!empty($datas['Cart']['voucher_code'])){
            if ((int)$datas['Voucher']['credit'] > 0){
                //On rajoute le crédit
                $credits+= (int)$datas['Voucher']['credit'];
                $coupon = true;
                $reduction_mode = 'credit';
            }elseif ((float)$datas['Voucher']['amount'] > 0){
                //On rajoute la réduction du prix
                $reduction_amount = (float)$datas['Voucher']['amount'] * (-1);
                $coupon = true;
                $reduction_mode = 'amount';
            }elseif ((float)$datas['Voucher']['percent'] > 0){
                //On rajoute la réduction du prix
                $reduction_amount = (float)$datas['Voucher']['percent'];
                $coupon = true;
                $reduction_mode = 'percent';
            }
			$vouch = (isset($datas['Voucher']['code']) && !empty($datas['Voucher']['code']))?$datas['Voucher']:false;
        }
		if(!empty($datas['GiftOrder']['code'])){
           if ((float)$datas['GiftOrder']['amount'] > 0){
                //On rajoute la réduction du prix
			    if ((float)$datas['GiftOrder']['valid'] == 2)
                	$reduction_amount = (float)$datas['GiftOrder']['sold'] * (-1);
				else
					$reduction_amount = (float)$datas['GiftOrder']['amount'] * (-1);
                $coupon = true;
                $reduction_mode = 'amount';
            }
			$vouch = (isset($datas['GiftOrder']['code']) && !empty($datas['GiftOrder']['code']))?$datas['GiftOrder']:false;
        }

        /* Calcul du total price avec coupon */
        $total_price = (float)$datas['Product']['tarif'];
        if (isset($reduction_mode)){
            switch ($reduction_mode){
                case 'credit':
                    $total_price = $total_price + $reduction_amount;
                    break;
                case 'amount':
                    $total_price = $total_price + $reduction_amount;
                    break;
                case 'percent':
                    $total_price = $total_price * (1 - ($reduction_amount / 100));
                    break;
            }
        }

        $cart = array(
            'id_cart' => $datas['Cart']['id'],
            'product' => array(
                'Product' => $datas['Product'],
                'Country' => $datas['Country'],
                'ProductLang' => array($datas['ProductLang'])
            ),
            'lang_id' => $datas['Lang']['id_lang'],
            'credits_without_voucher' => $credits_save,
            'credits_with_voucher' => $credits,
            'voucher'  => $vouch,
            'reduction_mode'   => $reduction_mode,
            'reduction_amount' => $reduction_amount,
            'user'  => $datas['User'],
            'total_price'   => $total_price,
            'total_price_before_reduc' => (float)$datas['Product']['tarif']
        );


        return $cart;
    }
}
