<?php

/**
 * @file
 * Runs affirm.js to get you to fill out info for Checkout Token
 *
 * @category   Testing
 * @package    Affirm API
 * @author     Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    0.5.0
 */
// Include the configuration directory
require_once ('webconfig.php');

$host = $_SERVER['HTTP_HOST'];
$root = $_SERVER['DOCUMENT_ROOT'];
$chars = strlen($root);
$dir = substr(__DIR__, $chars);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Affirm Tester</title>
    <script>
      var _affirm_config = {
      public_api_key:  "<?php echo $public_key; ?>",
      script:          "<?php echo $affirm_url; ?>"
      };
      (function(l,g,m,e,a,f,b){var d,c=l[m]||{},h=document.createElement(f),n=document.getElementsByTagName(f)[0],k=function(a,b,c){return function(){a[b]._.push([c,arguments])}};c[e]=k(c,e,"set");d=c[e];c[a]={};c[a]._=[];d._=[];c[a][b]=k(c,a,b);a=0;for(b="set add save post open empty reset on off trigger ready setProduct".split(" ");a<b.length;a++)d[b[a]]=k(c,e,b[a]);a=0;for(b=["get","token","url","items"];a<b.length;a++)d[b[a]]=function(){};h.async=!0;h.src=g[f];n.parentNode.insertBefore(h,n);delete g[f];d(g);l[m]=c})(window,_affirm_config,"affirm","checkout","ui","script","ready");
    </script>
    <script>
      // setup and configure checkout
        affirm.checkout(JSON.parse('{"config":{"financial_product_key":"<?php echo $product_key;?>"},"merchant":{"user_cancel_url":"https://<?php echo $host . $dir; ?>/","user_confirmation_url":"https://<?php echo $host . $dir; ?>/","user_confirmation_url_action":"POST"},"billing":{"name":{"first":"<?php echo $firstname; ?>","last":"<?php echo $lastname; ?>"},"address":{"line1":"<?php echo $addr; ?>","city":"<?php echo $city; ?>","state":"<?php echo $state;?>","zipcode":"<?php echo $zip; ?>","country":"<?php echo $nation; ?>"},"email":"<?php echo $email; ?>","phone_number":"<?php echo $phone; ?>","phone_number_alternative":""},"shipping":{"name":{"first":"<?php echo $firstname; ?>","last":"<?php echo $lastname; ?>"},"address":{"line1":"<?php echo $addr; ?>","city":"<?php echo $city; ?>","state":"<?php echo $state;?>","zipcode":"<?php echo $zip; ?>","country":"<?php echo $nation; ?>"},"email":"<?php echo $email; ?>","phone_number":"<?php echo $phone; ?>"},"items":[{"display_name":"Widget","sku":"w1337","unit_price":1300,"qty":1,"item_image_url":"https://<?php echo $host; ?>/img/w1337.jpg","item_url":"https://<?php echo $host;?>/product/w1337.php"}],"currency":"USD","discounts":[],"tax_amount":0,"shipping_amount":37,"total":1337}'))


        // submit and redirect to checkout flow
        affirm.checkout.post();
    </script>
  </head>
  <body>

  </body>
</html>
