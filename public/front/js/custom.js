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

  function replaceFragments(resp){
    if(resp.items_html !== undefined){
      $('#cart-items-body').html(resp.items_html);
    }
    if(resp.summary_html !== undefined){
      $('#cart-summary-container').html(resp.summary_html);
    }
    if(resp.totalCartItems !== undefined){
      $('.totalCartItems').text(resp.totalCartItems);
    }
  }

  function refreshCart(){
    $.get('/cart/refresh', function(resp){
      replaceFragments(resp);
    });
  }

  $(document).ready(refreshCart);

  // Add to cart
  $(document).on('submit', '#addToCart', function(e){
    e.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf},
      url: '/cart',
      type: 'POST',
      data: formData,
      success: function(resp){
        if(resp.status === true){
          $(".print-success-msg").html('<div class="alert alert-success">'+resp.message+'</div>').show().delay(3000).fadeOut();
          replaceFragments(resp);
        } else {
          $(".print-error-msg").html('<div class="alert alert-danger">'+(resp.message || 'Error')+'</div>').show().delay(3000).fadeOut();
        }
      },
      error: function(xhr){
        if(xhr.responseJSON && xhr.responseJSON.errors){
          const firstKey = Object.keys(xhr.responseJSON.errors)[0];
          const msg = xhr.responseJSON.errors[firstKey][0];
          $(".print-error-msg").html('<div class="alert alert-danger">'+msg+'</div>').show().delay(3000).fadeOut();
        } else {
          alert("Error");
        }
      }
    });
  });

  // Quantity +/- (PATCH)
  $(document).on('click','.updateCartQty',function(){
    const cartId = $(this).data('cart-id');
    const dir = $(this).data('dir');
    const input = $('.cart-qty[data-cart-id="'+cartId+'"]');
    let qty = parseInt(input.val() || '1', 10);
    qty = dir === 'up' ? qty + 1 : Math.max(1, qty - 1);

    $.ajax({
      url: '/cart/' + cartId,
      type: 'PATCH',
      headers: {'X-CSRF-TOKEN': csrf},
      data: {qty: qty},
      success: function(resp){
        if(resp.status === true){
          replaceFragments(resp);
        } else {
          alert(resp.message || 'Error updating cart!');
        }
      },
      error: function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
        alert(msg);
      }
    });
  });

  // Manual quantity input change
  $(document).on('change','.cart-qty',function(){
    const cartId = $(this).data('cart-id');
    let qty = parseInt($(this).val() || '1', 10);
    if(isNaN(qty) || qty < 1) qty = 1;

    $.ajax({
      url: '/cart/' + cartId,
      type: 'PATCH',
      headers: {'X-CSRF-TOKEN': csrf},
      data: {qty: qty},
      success: function(resp){
        if(resp.status === true){
          replaceFragments(resp);
        } else {
          alert(resp.message || 'Error updating cart!');
        }
      },
      error: function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
        alert(msg);
      } 
    });
  });

  // Remove item
  $(document).on('click','.removeCartItem',function(){
    const cartId = $(this).data('cart-id');
    $.ajax({
      url: '/cart/' + cartId,
      type: 'DELETE',
      headers: {'X-CSRF-TOKEN': csrf},
      success: function(resp){
        if(resp.status === true){
          replaceFragments(resp);
        } else {
          alert(resp.message || 'Error deleting item!');
        }
      },
      error: function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error";
        alert(msg);
      }
    });
  });
  // Apply coupon
  $(document).on('submit','#applyCouponForm',function(e){
    e.preventDefault();
    var code = $('#coupon_code').val().trim();
    if(!code){
      $('#coupon-msg').html('<div class="alert alert-danger">Coupon code is required</div>');
      return;
    }
    $.ajax({
      url: '/cart/apply-coupon',
      method: 'POST',
      headers: {'X-CSRF-TOKEN': csrf},
      data: {coupon_code: code},
      success: function(resp){
          $('#coupon-msg').html('<div class="alert alert-success">'+resp.message+'</div>');
          replaceFragments(resp);
        },
        error: function(xhr){
          if(xhr.responseJSON){
            const resp = xhr.responseJSON;
            $('#coupon-msg').html('<div class="alert alert-danger">'+(resp.message || 'Error')+'</div>');
            replaceFragments(resp);
          }else{
            $('#coupon-msg').html('<div class="alert alert-danger">Something went wrong</div>');
          }
        }
    });
  });

  // Remove coupon
  $(document).on('click','#removeCouponBtn',function(e){
    e.preventDefault();
    $.ajax({
     url: '/cart/remove-coupon',
      method: 'POST',
      headers: {'X-CSRF-TOKEN': csrf},
      success: function(resp){
          $('#coupon-msg').html('<div class="alert alert-success">'+resp.message+'</div>');
          replaceFragments(resp);
          $('#coupon_code').val('');
      },
      error: function(){
        $('#coupon-msg').html('<div class="alert alert-danger">Something went wrong</div>');
      }
    });
  });
})(jQuery);

$(document).on('submit', '#loginForm', function (e) {
  e.preventDefault();
  $('.help-block.text-danger').text('');
  
  const $btn = $('#loginButton');
  $btn.prop('disabled', true).text('Please wait...');

  // Use FormData (more reliable with Laravel)
  const formData = new FormData();
  formData.append('email', $('#loginEmail').val().trim().toLowerCase());
  formData.append('password', $('#loginPassword').val());
  formData.append('user_type', $('input[name="user_type"]:checked').val());
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

  $.ajax({
    url: window.routes && window.routes.userLoginPost
      ? window.routes.userLoginPost
      : '/user/login',
    method: 'POST',
    processData: false, // required for FormData
    contentType: false, // required for FormData
    data: formData,
    success: function (resp) {
      $btn.prop('disabled', false).text('Login');
      if (resp.success) {
        $('#loginSuccess').html(
          '<div class="alert alert-success">' + resp.message + '</div>'
        );
        window.location.href = resp.redirect || '/';
      } else {
        $('#loginSuccess').html(
          '<div class="alert alert-danger">Login Failed</div>'
        );
      }
    },
    error: function (xhr) {
      $btn.prop('disabled', false).text('Login');
      if (xhr.responseJSON && xhr.responseJSON.errors) {
        $.each(xhr.responseJSON.errors, function (key, val) {
          $('[data-error-for="' + key + '"]').text(val[0]);
        });
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
        $('#loginSuccess').html(
          '<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>'
        );
      } else {
        console.error(xhr.responseText || xhr);
        $('#loginSuccess').html(
          '<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>'
        );
      }
    },
  });
});

// Register

$(document).on('submit', '#registerForm', function(e){
    e.preventDefault();
    $('.help-block.text-danger').text('');
    var $btn = $('#registerButton');
    $btn.prop('disabled', true).text('Please wait...');

    // Use FormData instead of JSON for better compatibility
    var formData = new FormData();
    formData.append('name', $('#name').val());
    formData.append('email', $('#email').val().trim().toLowerCase());
    formData.append('password', $('#password').val());
    formData.append('password_confirmation', $('#password_confirmation').val());
    formData.append('user_type', $('input[name="user_type"]:checked').val());
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: window.routes && window.routes.userRegisterPost ? window.routes.userRegisterPost : '/user/register',
        method: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        success: function(resp){
            console.log('✅ SUCCESS:', resp);
            $btn.prop('disabled', false).text('Register');
            if(resp.success){
                $('#registerSuccess').html('<div class="alert alert-success">'+resp.message+'</div>');
                setTimeout(() => {
                    window.location.href = resp.redirect || '/';
                }, 1000);
            } else {
                $('#registerSuccess').html('<div class="alert alert-danger">Registration Failed</div>');
            }
        },
        error: function(xhr){
            console.group('❌ AJAX Error');
            console.log('Status:', xhr.status);
            console.log('Response:', xhr.responseJSON);
            console.groupEnd();

            $btn.prop('disabled', false).text('Register');

            if(xhr.responseJSON && xhr.responseJSON.errors){
                $.each(xhr.responseJSON.errors, function(key, val){
                    const $errorElement = $('[data-error-for="'+key+'"]');
                    if ($errorElement.length) {
                        $errorElement.text(val[0]);
                    } else {
                        // Fallback - show in success area
                        $('#registerSuccess').html('<div class="alert alert-danger">'+val[0]+'</div>');
                    }
                });
            } else if(xhr.responseJSON && xhr.responseJSON.message){
                $('#registerSuccess').html('<div class="alert alert-danger">'+xhr.responseJSON.message+'</div>');
            } else {
                $('#registerSuccess').html('<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>');
            }
        }
    });
});
