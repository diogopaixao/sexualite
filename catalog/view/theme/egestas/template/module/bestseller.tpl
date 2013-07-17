<?php 
	$option = $this->config->get('bestseller_module');
	if($option && is_array($option)) {
		$option = array_shift($option);
	}
?>
<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="box-product product-grid">
      <?php foreach ($products as $product) { ?>
      <div>
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
        <?php } else { ?>
        <div class="image"><span class="no-image" style="line-height: <?php echo $option['image_width']; ?>px"><img src="image/no_image.jpg" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></span></div>
        <?php } ?>
        <?php if ($product['price']) { ?>
        <div class="price">
          <?php if (!$product['special']) { ?>
          <div><span class="price-fixed"><?php echo $product['price']; ?></span></div>
          <?php } else { ?>
          <div class="special-price"><span class="price-old"><?php echo $product['price']; ?></span><span class="price-fixed"><?php echo $product['special']; ?></span></div>
          <?php } ?>
        </div>
        <?php } ?>
        <div class="name" style="width: <?php echo $option['image_width']; ?>px"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <?php if ($product['rating']) { ?>
        <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
        <?php } ?>
		<div class="details">
        <div class="cart"><input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="icon-cart-white button" /></div>
        <!-- <div class="wishlist"><a class="icon-basket" onclick="addToWishList('<?php echo $product['product_id']; ?>');">Wishlist</a></div>
        <div class="compare"><a class="icon-compare" onclick="addToCompare('<?php echo $product['product_id']; ?>');">Compare</a></div> -->
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
