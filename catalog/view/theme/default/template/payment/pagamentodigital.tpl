<form id="formpagamentodigital" action="<?php echo $action; ?>" method="post" />
  <input type="hidden" name="email_loja" value="<?php echo $email_pagamentodigital; ?>" />
  <input type="hidden" name="id_pedido" value="<?php echo $referencia; ?>" />
  <input type="hidden" name="redirect" value="true" />
  <input type="hidden" name="url_retorno" value="<?php echo $retorno; ?>" />
  <input type="hidden" name="tipo_integracao" value="PAD" />
<?php
/* Verifica se não tem cumpom de frete grátis */
if(!isset($fretegratis)){
  /* Verifica se foi selecionado um modo de envio */
  if(isset($this->session->data['shipping_method'])){
    /* Verifica se o método de envio e frete grátis */
    if($this->session->data['shipping_method']['id'] == 'free.free'){
      $valorfe = 0;
    } else {
	  /* Pega o valor do frete já calculado */
      $valorfe = str_replace(',','.', substr($this->session->data['shipping_method']['text'],2));
      if ($valorfe<1) {
	    /* Se o valor do frete for 0 então deu erro no cálculo */
        $errofrete = true;		
      }
    }
  } else {
	$errofrete = true; 			
  }
/* Se existe o valor é 0 */  
} else if($fretegratis==true) {
  $valorfe = 0;
}

/* Dados dos produtos */
$i = 1;
$iddoproduto = array();
foreach ($products as $product) {
  /* Id do produto */
  $produtoid = $product['id'];
  /* Valor do produto */
  $preco = number_format($product['valor'], 2, '.', '');
  /* Peso do produto */
  $pesoprod = preg_replace("/[^0-9]/", "", $product['peso']);
  /* Descrição dos produtos */
  $descricaoproduto = $product['descricao']; 
  /* Monta os opcionais do produto */
  $dopcoes = '';
  foreach ($product['option'] as $option) { 
     $dopcoes.=' -'.$option['name'].' '.$option['value'];
  }
  /* Incrementa os opcionais a descrição do produto */
  $descricaoproduto.= $dopcoes;
  /* Id do produto */
  $produtoid = str_replace($iddoproduto, $produtoid."#".$i, $produtoid);
  /* Lista o produto */
  echo '<input type="hidden" name="produto_codigo_'.$i.'" value="'.$produtoid.'" />' ."\n" ;
  echo '<input type="hidden" name="produto_descricao_'.$i.'" value="'.$descricaoproduto.'" />' ."\n";
  echo '<input type="hidden" name="produto_qtde_'.$i.'" value="'.$product['quantidade'].'" />' ."\n";
  echo '<input type="hidden" name="produto_valor_'.$i.'" value="'.$preco.'" />' ."\n";
  
  $i++;
  $iddoproduto[] = $produtoid;	
}

/* Remove os caracteres não numéricos do Cep */
$cep= preg_replace("/[^0-9]/", "",$cep);

/* Se não deu erro no frete envia o valor do frete */
if(!isset($errofrete)){
  echo '<input type="hidden" name="frete" value="'.$valorfe.'" />' ."\n";
}
?>
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="nome" value="<?php echo $primeiro_nome .' '. $ultimo_nome; ?>" />
  <input type="hidden" name="endereco" value="<?php echo $endereco; ?>">
  <input type="hidden" name="complemento" value="<?php echo $complemento; ?>">
  <input type="hidden" name="cidade" value="<?php echo $cidade; ?>">
  <input type="hidden" name="estado" value="<?php echo $estado; ?>">
  <input type="hidden" name="cep" value="<?php echo $cep; ?>">

<?php if (isset($valordesconto)==true) { ?>
  <input type="hidden" name="desconto" value="<?php echo $valordesconto; ?>" /> 
<?php } ?>

</form>

<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
  <br />
  <center>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="468" height="60">
    <param name="movie" value="https://www.pagamentodigital.com.br/site/banner/campanhas/_24x/05/PD_Institucional_468x60.swf?oas=https://www.pagamentodigital.com.br" />
    <param name="quality" value="high" />
    <embed src="https://www.pagamentodigital.com.br/site/banner/campanhas/_24x/05/PD_Institucional_468x60.swf?oas=https://www.pagamentodigital.com.br" quality="high" pluginspage="https://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="468" height="60"></embed>
    </object>
  </center>
  <br />
Ap&oacute;s clicar no bot&atilde;o <b>Comprar</b> que est&aacute; abaixo, voc&ecirc; ser&aacute; redirecionado para o Pagamento Digital para efetuar o pagamento.
  <br />
  <br />
</div>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a id="checkout" ><span><img src="https://www.pagamentodigital.com.br/webroot/img/bt_comprar.gif" border="0" /></span></a></td>
    </tr>
  </table>
</div>
<script type="text/javascript"><!--
$('#checkout').click(function() {
 $('body').css("cursor", "wait");
$('#checkout').hide('fast');
	$.ajax({ 
		type: 'GET',
		url: 'index.php?route=payment/pagamentodigital/confirm',
		success: function() {
			 $('#formpagamentodigital').submit();
		}		
	});
});
//--></script>