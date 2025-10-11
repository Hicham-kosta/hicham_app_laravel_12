$(document).ready(function() {
    /*$('#sortForm').on('change', function() {
        this.form.submit();
    });*/
    $('#search_input').on('keyup', function() {
        let query = $(this).val();
        if (query.length > 2) {
            // AJAX request
            $.ajax({
                url: '/search-products',
                method: 'GET',
                data: { q: query},
                success: function(data) {
                    // Display results in the search_result div
                    $('#search_result').html(data);
                }
            });
        }else{
            $('#search_result').html('');
        }
    });
});

$(document).on("change", ".getPrice", function() {
    var size = $(this).val();
    var product_id = $(this).data('product-id');
    $.ajax({
        url: '/get-product-price',
        type: 'POST',
        data: {size: size, product_id: product_id, _token: $('meta[name="csrf-token"]').attr('content')},
        success: function(resp) {
            if(!resp || resp.status === false){
                return;
            }
            if(resp.discount > 0){
                $(".getAttributePrice").html(
                    "<span class='text-danger final-price'> $"+ resp.final_price +" </span>" +
                    "<del class='text-muted original-price'> $"+ resp.product_price +" </del>"
                );
            }else{
                $(".getAttributePrice").html(
                    "<span class='final-price'>$"+ resp.final_price +"</span>"
                );
            }
        }  
    });
});

// Add to cart - product detail page


(function($){
    const csrf = $('meta[name="csrf-token"]').attr('content');
   function refreshCart(){
      $.get('/cart/refresh', function(resp){
        $('#cart-items-body').html(resp.items_html);
        $('#cart-summary-container').html(resp.summary_html);
    });
  }
  $(document).ready(function(){
    refreshCart();
  });

  // Add to cart (POST/cart)
  $(document).on('submit', '#addToCart', function(e){
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf},
      url: '/cart',
      type: 'POST',
      data: formData,
      success: function(resp){
        if(resp.status === true){
            $(".print-success-msg").html('<div class="alert alert-success">'+resp.message+'</div>').show().delay(3000).fadeOut();
            refreshCart();
      }else{
            $(".print-error-msg").html('<div class="alert alert-danger">'+resp.message+'</div>').show().delay(3000).fadeOut();
      }
     },
        error: function(xhr){
            if(xhr.responseJSON && xhr.responseJSON.errors){
                const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                const msg = xhr.responseJSON.errors[firstKey][0];
                $(".print-error-msg").html('<div class="alert alert-danger">'+msg+'</div>').show().delay(3000).fadeOut();
            }else{
                alert("Error")
            }
          }
    });
  });
  // plus/minius update (PATCH/cart/{id})
  $(document).on('click','.updateCartQty',function(e){
    const cartId = $(this).data('cart-id');
    const dir = $(this).data('dir');
    const input = $('.cart-qty[data-cart-id="'+cartId+'"]');
    let qty = parseInt(input.val() || '1', 10);
    qty = dir === 'up' ? qty + 1 : Math.max(1, qty - 1);
    $.ajax({
        url: '/cart/'+cartId,
        type: 'PATCH',
        headers: {'X-CSRF-TOKEN': csrf},
        data: {qty: qty},
        success: function(resp){
            $('#cart-items-body').html(resp.items_html);
            $('#cart-summary-container').html(resp.summary_html); 
        },
        error: function(xhr){
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
            alert(msg);
        }
    });
  });
  // Manual qty change -> PATCH
    $(document).on('change','.cart-qty',function(e){
        const cartId = $(this).data('cart-id');
        let qty = parseInt($(this).val(), 10);
        if(isNaN(qty) || qty < 1){
            qty = 1;
        }
        $.ajax({
            url: '/cart/'+cartId,
            type: 'PATCH',
            headers: {'X-CSRF-TOKEN': csrf},
            data: {qty: qty},
            success: function(resp){
                $('#cart-items-body').html(resp.items_html);
                $('#cart-summary-container').html(resp.summary_html); 
            },
            error: function(xhr){
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
                alert(msg);
            }
            
        });
    });
    // Remove item -> DELETE/cart{id}
    $(document).on('click','.removeCartItem',function(){
        const cartId = $(this).data('cart-id');
        $.ajax({
            url: '/cart/'+cartId,
            type: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrf},
            success: function(resp){
                $('#cart-items-body').html(resp.items_html);
                $('#cart-summary-container').html(resp.summary_html); 
            },
            error: function(xhr){
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
                alert(msg);
            }
        });
    });
})(jQuery);


