@forelse($cartItems as $item)
   <tr>
    <td class="align-middle text-left">
      <img src="{{$item['image']}}" alt="{{$item['product_name']}}" style="width: 50px;">
      <a class="ml-2" href="{{url($item['product_url'])}}">{{$item['product_name']}}</a>
          <div class="small text-muted">Size: {{$item['size']}}</div>
    </td>
    <td class="align-middle">${{$item['unit_price']}}</td>
    <td class="align-middle">
       <div class="input-group-quantity mx-auto" style="width: 100px;">
         <div class="input-group-btn">
            <button type="button" class="btn btn-sm btn-primary btn-plus updateCartQty" 
            data-cart-id="{{$item['cart_id']}}" data-dir="up"><i class="fa fa-plus"></i></button>
          </div>
         <input type="text" class="form-control form-control-sm bg-secondary text-center cart-qty" 
         value="{{$item['qty']}}" data-cart-id="{{$item['cart_id']}}">
         <div class="input-group-btn">
            <button type="button" class="btn btn-sm btn-primary btn-minus updateCartQty" 
            data-cart-id="{{$item['cart_id']}}" data-dir="down"><i class="fa fa-minus"></i></button>
         </div> 
       </div>
    </td>
    <td class="align-middle">${{$item['line_total']}}</td>
    <td class="align-middle">
        <button type="button" class="btn btn-sm btn-primary removeCartItem" 
        data-cart-id="{{$item['cart_id']}}"><i class="fa fa-times"></i></button>
    </td>    
</tr>
@empty
   <tr><td colspan="5" class="text-center">No items in cart</td></tr>
@endforelse