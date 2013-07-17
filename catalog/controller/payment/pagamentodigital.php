<?php
class ControllerPaymentPagamentoDigital extends Controller {
  
  protected function index() {
    
    $this->data['button_confirm'] = $this->language->get('button_confirm');
    
    $this->data['button_back'] = $this->language->get('button_back');
    
    $this->load->model('checkout/order');
    
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    
    /* E-mail para identificação da conta */
    $this->data['email_pagamentodigital'] = $this->config->get('pagamentodigital_email');
	
	/* URL de retorno para verificar o status da venda */
    $this->data['retorno'] = HTTPS_SERVER . 'payment/pagamentodigital/callback';
    
    $this->load->library('encryption');
    
    $encryption = new Encryption($this->config->get('config_encryption'));
	
    /* Código da transação */
    $this->data['referencia'] = $order_info['order_id']; 
    
    /* Código da moeda */
    $this->data['moeda'] = $order_info['currency'];
       
	/* Valor total do pedido */
	$this->data['valor_total'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
    
    /* Nome do cliente */
    $this->data['primeiro_nome'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
    
    /* Sobrenome do cliente */
    $this->data['ultimo_nome'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
	
    /* Endereço */
    $this->data['endereco'] =  html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
    
    /* Complemento */
    $this->data['complemento'] =  html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
       
    /* Cidade */
    $this->data['cidade'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
    
    /* CEP */
    $this->data['cep'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
   
    /* País */
    $this->data['pais'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
    
    /* E-mail do cliente */
    $this->data['email'] = $order_info['email'];
    
	/* Identificação extensa da fatura */
	$this->data['fatura'] = $this->session->data['order_id'] . ' - ' . $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
    /* Pega o id do país */		
    $this->load->model('localisation/country');
    $paises = $this->model_localisation_country->getCountries();		
    foreach ($paises as $country) {
      if($country['name']==$order_info['payment_country']){
        $codigodopais = $country['country_id'];
      }
    }
	
    /* Com id do país pega code da cidade */
    $this->load->model('localisation/zone');
    $results = $this->model_localisation_zone->getZonesByCountryId($codigodopais);
    foreach ($results as $result) {
      if($result['name']==$order_info['payment_zone']){
        $this->data['estado'] = $result['code'];
      }
    } 
	
    /* DDD e telefone do cliente */	
    if(isset($order_info['ddd'])){
      $this->data['ddd'] = $order_info['ddd'];
    } else {
      $ntelefone = preg_replace("/[^0-9]/", "", $order_info['telephone']);	
      if(strlen($ntelefone) >= 10){	
        $ntelefone = ltrim($ntelefone, "0");
        $this->data['ddd'] = substr($ntelefone, 0, 2);
        $this->data['telefone'] = substr($ntelefone, 2,11);
      } else {
        $this->data['telefone'] = substr($ntelefone, 2,11);
      }
    }
		
	/* Verifica o valor do desconto e se tem frete grátis */
	if (isset($this->session->data['coupon'])) {
	  $this->load->model('checkout/coupon');
	  $coupon = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
	  if (isset($coupon)) {  
	    /* valor do desconto, retira o R$ , o ponto separado do milhar e altera a , por pronto. Ex: R$1.048,88 ficara' 1048.88 */
	    $desconto = str_replace(',','.', substr( str_replace('.','',$this->currency->format($coupon['discount']) ),2) ) ; 
	    /* Valor total da compra */
	    $valototal = str_replace(',','.', substr( str_replace('.','',$this->currency->format($this->cart->getTotal()) ),2) );
	    /* Valor total para que seja aceito o desconto, Ex: Desconto para compras acima de R$100 */
	    $desctotalcompra = str_replace(',','.', substr( str_replace('.','',$this->currency->format($coupon['total']) ),2) ); 
	    if($valototal>=$desctotalcompra){
	      $this->data['cupomnome'] = $coupon['name'];
	      /**
	      * $coupon['type']='P'    -> Desconto Por Porcentagem
	      * $coupon['type']='F'    -> Desconto Fixo
	      * $coupon['shipping']=0  -> Frete gratis: NAO
	      * $coupon['shipping']=1  -> Frete gratis: SIM
	      */
	      if($coupon['type']=='P'){
	        $valordesconto = ($valototal * ($desconto/100));
	        $this->data['valordesconto'] = $valordesconto;
	      } else if($coupon['type']=='F') {
	        $this->data['valordesconto'] = $desconto;
	      }
	      if ($coupon['shipping'] == 1){
	        $this->data['fretegratis'] = true;
	      }	 
	    }
	  }
	} 
 		
    /* Faz a listagem dos produtos */
    $this->data['products'] = array();
    foreach ($this->cart->getProducts() as $product) {
      $option_data = array();
      foreach ($product['option'] as $option) {
        $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => $option['value']
                         );
      } 
      if(isset($coupon)){	
        if($coupon['type']=='P'){
          $valorddescon = ($restocupom/100)*$product['price'];
          $valorproduto=($product['price']-$valorddescon);
        }else if($coupon['type']=="F"){
          if($trancaproduto==1){
            if($restocupom<=$product['price']){
              $valorproduto=($product['price']-$restocupom);
              $trancaproduto=2;
            }else{
              $restocupom=($restocupom-$product['price']);	
              $valorproduto=0;
            }
          }else{
            $valorproduto=$product['price'];
          }
        }
      }else{	
        $valorproduto = $product['price'];
      }
	  
      $this->data['products'][] = array(
				'descricao'  => $product['name'],
				'valor'      => $product['price'],
				'quantidade' => $product['quantity'],
				'option'     => $option_data,
				'id'         => $product['product_id'],	
				'peso'       => $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class'))
                ); 
    } 

    $this->data['action'] = 'https://www.pagamentodigital.com.br/checkout/pay/';

    $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		
    $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/success';

    if ($this->request->get['route'] != 'checkout/guest_step_3') {
      $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
    }else{
      $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
    }	
		
    $this->id = 'payment';

    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagamentodigital.tpl')) {
      $this->template = $this->config->get('config_template') . '/template/payment/pagamentodigital.tpl';
    }else{
      $this->template = 'default/template/payment/pagamentodigital.tpl';
    }	
		
    $this->render();
  }
	
  public function confirm() {
    
	$this->language->load('payment/pagamentodigital');
		
    $order_id = $_SESSION['order_id'];
	
	$this->load->model('checkout/order');
	
	$order_info = $this->model_checkout_order->getOrder($order_id);
		
    $comment  = $this->language->get('text_instruction') . "\n\n";
    $comment .= $this->language->get('text_payment');
		
    $this->model_checkout_order->confirm($order_id, $this->config->get('pagamentodigital_order_andamento'), $comment);

    /* Limpa a sessão */
    if (isset($this->session->data['order_id'])) {
      $this->cart->clear();
      unset($this->session->data['shipping_method']);
      unset($this->session->data['shipping_methods']);
      unset($this->session->data['payment_method']);
      unset($this->session->data['payment_methods']);
      unset($this->session->data['comment']);
      unset($this->session->data['order_id']);	
      unset($this->session->data['coupon']);
    }
  
  }
		
  public function callback() {
  
    /* Carrega o TOKEN para acesso ao Pagamento Digital */
    $token = $this->config->get('pagamentodigital_token');

    /* Variáveis de retorno */
    $id_transacao = $_REQUEST['id_transacao'];
    $valor_original = $_REQUEST['valor_original'];
    $valor_loja = $_REQUEST['valor_loja'];
    $status = $_REQUEST['status'];
    $id_pedido = $_REQUEST['id_pedido'];
  	  
    $post = "transacao=$id_transacao" .
    "&status=$status" .
    "&valor_original=$valor_original" .
    "&valor_loja=$valor_loja" .
    "&token=$token";
    $enderecoPost = "https://www.pagamentodigital.com.br/checkout/verify/";
	  
    ob_start();
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $enderecoPost);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    curl_exec ($ch);
    $resposta = ob_get_contents();
    ob_end_clean();
	  
    if(trim($resposta)=="VERIFICADO"){
      $order_id = $_SESSION['order_id'];
      $this->load->model('checkout/order');
      $order_info = $this->model_checkout_order->getOrder($order_id);
      /* htmlentities porque o Pagamento Digital retorna o status como ISO-8859-1 e esse script e' em UTF-8, usei dessa forma para nao dar erro ao fazer o switch.*/
      $this->model_checkout_order->confirm($order_id, htmlentities($status));
	  switch(htmlentities($status)){
        case 'Transa&ccedil;&atilde;o Conclu&iacute;da':
          $this->model_checkout_order->update($order_id, $this->config->get('pagamentodigital_order_concluido'), '', TRUE);
          $this->model_checkout_order->confirm($order_id, $this->config->get('pagamentodigital_order_concluido'), '', TRUE);
        break;
        case 'Transa&ccedil;&atilde;o em Andamento':
          $this->model_checkout_order->update($order_id, $this->config->get('pagamentodigital_order_andamento'), '', TRUE);
          $this->model_checkout_order->confirm($order_id, $this->config->get('pagamentodigital_order_andamento'), '', TRUE);
        break;
        case 'Transa&ccedil;&atilde;o Cancelada':
          $this->model_checkout_order->update($order_id, $this->config->get('pagamentodigital_order_cancelado'), '', TRUE);
          $this->model_checkout_order->confirm($order_id, $this->config->get('pagamentodigital_order_cancelado'), '', TRUE);			
        break;
      }
      header('location:'. HTTPS_SERVER . 'checkout/success' );
    }
  }

}
?>
