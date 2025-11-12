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
            if(!resp || resp.status === false) return;
            var finalFormatted = resp.final_price_formatted || resp.final_price_display || '';
            var baseFormatted = resp.product_price_formatted || resp.product_price_display || '';
            if(!finalFormatted && typeof resp.final_price !== 'undefined') finalFormatted = Number(resp.final_price).toFixed(2);
            if(!baseFormatted && typeof resp.product_price !== 'undefined') baseFormatted = Number(resp.product_price).toFixed(2);
            if(resp.discount > 0 || (resp.percent && Number(resp.percent) > 0)){
                $(".getAttributePrice").html(
                    "<span class='text-danger final-price'> "+ finalFormatted +" </span>" +
                    "<del class='text-muted original-price'> "+ baseFormatted +" </del>"
                );
            }else{
                $(".getAttributePrice").html(
                    "<span class='final-price'>$"+ finalFormatted +"</span>"
                );
            }
            if(resp.final_price_formatted)$('mini-price').text(resp.final_price_formatted);
        },
        error: function(xhr, status, err){
          console.error('Error fetching price: ', err || status);
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

// Currency Switcher
(function(){
  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }
  var btn = document.getElementById('current-currency-btn'),
  list = document.getElementById('currency-list');
  if(!btn || !list) return;
  btn.addEventListener('click', function(e){
    e.stopPropagation();
    list.classList.toggle('show'); });
    document.addEventListener('click', function(){
      list.classList.remove('show'); });
      list.addEventListener('click', function(e){
        e.stopPropagation(); });
        var switchUrl = (window.appConfig && window.appConfig.switchCurrencyUrl) ? window.appConfig.switchCurrencyUrl 
        : '/currency/switch';
        var csrfToken = getCsrfToken();
        document.querySelectorAll('.currency-item').forEach(function(el){
          el.addEventListener('click', function(){
            var code = this.getAttribute('data-code');
            if(!code) return;
            fetch(switchUrl, {
              method: 'POST', credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'
              },
              body: JSON.stringify({code: code})
            }).then(function(res){
              return res.json().catch(function(){ return {}; 
              });
            }).then(function(resp){
              if(resp && resp.status === 'success') location.reload();
              else alert(resp.message || 'Something went wrong, please try again later.');
            }).catch(function(err){
              console.error(err);
              alert('Network error');
            });
          });
        });
})();

(function(){
  // Delay init until DOM is ready
  document.addEventListener('DOMContentLoaded', function(){
    const starContainer = document.getElementById('star-rating');
    const ratingInput = document.getElementById('ratingInput');
    const reviewForm = document.getElementById('reviewForm');

    console.log('[review debug] init', {starContainer: !!starContainer, ratingInput: !!ratingInput, reviewForm: !!reviewForm});

    if(!starContainer || !ratingInput) {
      console.warn('[review debug] starContainer or ratingInput missing. Aborting star init.');
      return;
    }

    // Ensure there is exactly one form/input/container (defensive)

    try{
      if(document.querySelectorAll('#star-rating').length !== 1) console.warn('[review debug] #star-rating count:', 
        document.querySelectorAll('#star-rating').length);

      if(document.querySelectorAll('#ratingInput').length !== 1) console.warn('[review debug] #ratingInput count:',
        document.querySelectorAll('#ratingInput').length);
    }catch(e){
      console.error( e);
    }

    // Event delegation handle click/hover on the container
    function setVisual(value){
      const stars = starContainer.querySelectorAll('i[data-value]');
      stars.forEach(s=>{
        const v = parseInt(s.getAttribute('data-value')||0,10);
        if(v <= value) {s.classList.remove('far'); s.classList.add('fas');}
        else {s.classList.remove('fas'); s.classList.add('far');}
        
      });
    }

    // Initilize from existing value
    const initial = parseInt(ratingInput.value||'0',10)||0;
    if(initial) setVisual(initial);

    // single handler for clicks
    starContainer.addEventListener('click', function(evt){
      const el = evt.target.closest('i[data-value]');
      if(!el) return;
      const val = parseInt(el.getAttribute('data-value')||0,10)||0;
      ratingInput.value = val;
      setVisual(val);
      console.log('[review debug] clicked', val);
    });

    // mouseover/out handlers (on container)
    starContainer.addEventListener('mouseover', function(evt){
      const el = evt.target.closest('i[data-value]');
      if(!el) return;
      const val = parseInt(el.getAttribute('data-value')||0,10)||0;
      setVisual(val);
    }, true);

    starContainer.addEventListener('mouseout', function(evt){
      // Restore to selected rating
      const current = parseInt(ratingInput.value||'0',10)||0;
      setVisual(current);
    }, true);

    // AJAX submit -- if you want it; otherwise, just use the normal form submit
    if(reviewForm) {
      reviewForm.addEventListener('submit', function(e){
        // if you prefere non-AJAX remove this block and server will redirect with flash
        e.preventDefault();
        if(!ratingInput.value || ratingInput.value == 0){
          alert('Please select a rating (1-5)');
          return;
        }
        const tokenEl = reviewForm.querySelector('input[name="_token"]');
        const token = tokenEl ? tokenEl.value : 
        (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

        const fd = new FormData(reviewForm);
        fetch(reviewForm.action, {
          method: 'POST',
          headers:{'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
          body: fd,
          credentials: 'same-origin',
        }).then(async res=>{
          const json = await res.json().catch(()=>null);
          if(res.ok){
            // show inline massage (simple)
            const parent = reviewForm.parentElement;
            const old = parent.querySelector('.ajax-review-alert');
            if(old) old.remove();
            const div = document.createElement('div');
            div.className = 'ajax-review-alert alert alert-success mt-3';
            div.innerHTML = (json && json.message) ? json.message :'Thank you for your review!';
            parent.insertBefore(div, parent.firstChild);
            // reset form
            reviewForm.reset();
            // reset stars
            ratingInput.value = 0;
            setVisual(0);
          }else{
            // show error
            const parent = reviewForm.parentElement;
            const old = parent.querySelector('.ajax-review-alert');
            if(old) old.remove();
            const div = document.createElement('div');
            div.className = 'ajax-review-alert alert alert-danger mt-3';
            let msg = 'Unable to submit review';
            if(json && json.message) msg = json.message;
            else if(json && json.errors) msg = Object.values(json.errors).join('<br>');
            div.innerHTML = msg;
            parent.insertBefore(div, parent.firstChild);

          }
        }).catch(err=>{
          console.error('[review debug] error', err);
          alert('Server error: try again later');
        });
        
      });
    }
  }); // DOMContentLoaded
})();

(async function(){
  'use strict';
  
  function getCsrfToken() {
    if(window.App && window.App.csrfToken) return window.App.csrfToken;
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }
  const csrfToken = getCsrfToken();
  
  function showAlert(containerId, html){
    const el = document.getElementById(containerId);
    if(el) el.innerHTML = html;
  }
  
  function clearFieldsErrors(){
    document.querySelectorAll('[data-error-for]').forEach(el => el.innerText = '');
  }
  
  function displayFieldErrors(errors){
    for(const key in errors){
      const el = document.querySelector('[data-error-for="'+key+'"]');
      if(el) el.innerText = errors[key][0];
    }
  }
  
  async function handleFetch(fetchPromise, btn, originalText, successCallback){
    try{
      const res = await fetchPromise;
      btn.disabled = false;
      btn.innerText = originalText;
      
      console.log('Response status:', res.status); // Debug log
      
      // Try to parse response as JSON
      let json;
      try {
        const text = await res.text();
        json = text ? JSON.parse(text) : {};
        console.log('Response data:', json); // Debug log
      } catch (parseError) {
        console.error('Failed to parse response:', parseError);
        showAlert('forgotSuccess', '<div class="alert alert-danger">Invalid response from server.</div>');
        return;
      }
      
      if(res.ok){
        if(successCallback) successCallback(json);
        return;
      }
      
      if(res.status === 422){
        displayFieldErrors(json.errors || {});
        return;
      }
      
      // Show specific error message from server if available
      const errorMessage = json.message || json.error || 'An error occurred. Please try again.';
      showAlert('forgotSuccess', '<div class="alert alert-danger">' + errorMessage + '</div>');
      
    }catch(err){
      btn.disabled = false;
      btn.innerText = originalText;
      console.error('Network error:', err);
      showAlert('forgotSuccess', '<div class="alert alert-danger">Network error. Please check your connection.</div>');
    }
  }
  
  document.addEventListener('DOMContentLoaded', function(){
    const csrfToken = getCsrfToken();
    
    // Forgot Form
    const forgotForm = document.getElementById('forgotForm');
    if(forgotForm) {
      const forgotBtn = document.getElementById('forgotBtn');
      forgotForm.addEventListener('submit', function(e){
        e.preventDefault();
        clearFieldsErrors();
        showAlert('forgotSuccess', '');
        
        if(!forgotBtn) {
          console.error('Forgot button not found!');
          return;
        }
        
        const originalText = forgotBtn.innerText;
        forgotBtn.disabled = true;
        forgotBtn.innerText = 'Sending...';
        
        const email = forgotForm.email ? forgotForm.email.value : '';
        const url = window.App && window.App.routes && window.App.routes.forgotPost 
          ? window.App.routes.forgotPost 
          : '/user/password/forgot';
        
        console.log('Sending request to:', url, 'with email:', email);
        
        const fetchPromise = fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken, 
            'Accept': 'application/json', 
            'Content-Type': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({email: email}),
        });
        
        handleFetch(fetchPromise, forgotBtn, originalText, function(json) {
          const message = json.message || 'Reset link sent.';
          showAlert('forgotSuccess', '<div class="alert alert-success">' + message + '</div>');
          // Clear form on success
          forgotForm.reset();
        });
      });
    }
  });
  // In your reset form event listener, add this:
// Fixed Reset Form Handler
// Fixed Reset Form Handler with better error handling
const resetForm = document.getElementById('resetForm');
if (resetForm) {
  const resetBtn = document.getElementById('resetBtn');
  resetForm.addEventListener('submit', function(e){
    e.preventDefault();
    console.log('Reset form submitted');

    clearFieldsErrors();
    showAlert('resetSuccess', '');

    if (!resetBtn) {
      console.error('Reset button not found!');
      return;
    }

    const originalText = resetBtn.innerText;
    resetBtn.disabled = true;
    resetBtn.innerText = 'Resetting...';

    // Collect form data
    const formData = new FormData(resetForm);
    const payload = {
      token: formData.get('token') || '',
      email: formData.get('email') || '',
      password: formData.get('password') || '',
      password_confirmation: formData.get('password_confirmation') || '',
    };

    console.log('Sending reset payload:', payload);

    const resetUrl = window.App?.resetUrl || '/user/password/reset';
    const csrfToken = window.App?.csrfToken || getCsrfToken();

    const fetchPromise = fetch(resetUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload)
    });

    handleFetch(fetchPromise, resetBtn, originalText, function(json) {
      console.log('Reset successful:', json);
      showAlert('resetSuccess', '<div class="alert alert-success">' + (json.message || 'Password reset successfully.') + '</div>');
      
      // Clear form on success
      resetForm.reset();
      
      if (json.redirect) {
        console.log('Redirecting to:', json.redirect);
        setTimeout(() => {
          window.location.href = json.redirect;
        }, 1500);
      }
    });
  });
}
})();

/*document.addEventListener('DOMContentLoaded', function() {
    // Get elements with null checks
    const accountForm = document.getElementById('accountForm');
    const countrySelect = document.getElementById('country');
    const countySelect = document.getElementById('county_select');
    const countySelectWrapper = document.getElementById('county_select_wrapper');
    const countyTextWrapper = document.getElementById('county_text_wrapper');
    const countyTextInput = document.getElementById('county_text');
    const postcodeInput = document.getElementById('postcode');
    const postcodeLoader = document.getElementById('postcode_loader');
    const saveBtn = document.getElementById('SaveBtn');
    const accountSuccess = document.getElementById('accountSuccess');

    // Check if all required elements exist
    if (!accountForm || !countrySelect) {
        console.error('Required form elements not found');
        return;
    }

    // Set the form action to match your route
    accountForm.action = '{{ route("user.account.update") }}';

    // Initialize form state
    function initializeForm() {
        const selectedCountry = countrySelect.value;
        toggleCountyFields(selectedCountry);
    }

    // Toggle between county dropdown and text input with null checks
    function toggleCountyFields(country) {
        if (!countySelectWrapper || !countyTextWrapper) {
            console.warn('County wrapper elements not found');
            return;
        }

        if (country === 'United States') {
            countySelectWrapper.style.display = 'block';
            countyTextWrapper.style.display = 'none';
            
            if (countySelect && countyTextInput) {
                countySelect.disabled = false;
                countyTextInput.disabled = true;
            }
        } else {
            countySelectWrapper.style.display = 'none';
            countyTextWrapper.style.display = 'block';
            
            if (countySelect && countyTextInput) {
                countySelect.disabled = true;
                countyTextInput.disabled = false;
            }
        }
    }

    // Country change handler
    countrySelect.addEventListener('change', function() {
        const selectedCountry = this.value;
        toggleCountyFields(selectedCountry);
    });

    // Postcode lookup with debouncing
    let postcodeTimeout;
    if (postcodeInput && postcodeLoader) {
        postcodeInput.addEventListener('input', function() {
            const postcode = this.value.trim();
            
            clearTimeout(postcodeTimeout);
            postcodeLoader.style.display = 'none';
            
            // Basic validation for UK postcodes
            if (postcode.length >= 5 && /^[A-Za-z0-9 ]+$/.test(postcode)) {
                postcodeLoader.style.display = 'block';
                
                postcodeTimeout = setTimeout(() => {
                    lookupPostcode(postcode);
                }, 800);
            }
        });
    }

    // Postcode lookup function
    function lookupPostcode(postcode) {
        // Use the correct route - fix the 404 error
        const url = '{{ route("user.postcode.lookup", ["postcode" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', encodeURIComponent(postcode));
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (postcodeLoader) postcodeLoader.style.display = 'none';
            
            if (data.success) {
                // Auto-fill city
                const cityInput = document.getElementById('city');
                if (cityInput && (!cityInput.value || cityInput.value.trim() === '')) {
                    cityInput.value = data.data.city || '';
                }
                
                // Auto-fill county/state based on country
                const selectedCountry = countrySelect.value;
                if (selectedCountry === 'United States') {
                    if (countySelect && data.data.state) {
                        const stateName = data.data.state;
                        const option = Array.from(countySelect.options).find(opt => 
                            opt.value.toLowerCase() === stateName.toLowerCase() ||
                            opt.text.toLowerCase() === stateName.toLowerCase()
                        );
                        if (option) {
                            countySelect.value = option.value;
                        } else {
                            showMessage(`State "${stateName}" not found in list. Please select manually.`, 'warning');
                        }
                    }
                } else {
                    if (countyTextInput && (!countyTextInput.value || countyTextInput.value.trim() === '')) {
                        countyTextInput.value = data.data.state || '';
                    }
                }
                
                showMessage('Postcode lookup successful! Address details auto-filled.', 'success');
            } else {
                showMessage('Postcode not found. Please enter address details manually.', 'warning');
            }
        })
        .catch(error => {
            console.error('Postcode lookup error:', error);
            if (postcodeLoader) postcodeLoader.style.display = 'none';
            showMessage('Postcode lookup service temporarily unavailable. Please enter details manually.', 'error');
        });
    }

    // Form submission handler
    accountForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button and show loading state
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        
        // Clear previous messages
        clearMessages();
        
        // Handle county field before submission
        const selectedCountry = countrySelect.value;
        if (selectedCountry === 'United States') {
            // Remove text input from form data
            if (countyTextInput) countyTextInput.disabled = true;
        } else {
            // Remove select from form data
            if (countySelect) countySelect.disabled = true;
        }
        
        // Prepare form data
        const formData = new FormData(this);
        
        // Log for debugging
        console.log('Submitting form data:', Object.fromEntries(formData));
        
        // Submit via AJAX
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                showMessage('Account updated successfully!', 'success');
                if (data.user) {
                    updateFormFields(data.user);
                }
            } else {
                if (data.errors) {
                    displayFormErrors(data.errors);
                } else {
                    showMessage(data.message || 'Failed to update account.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            showMessage('An error occurred while updating your account. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable all fields and submit button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
            if (countySelect) countySelect.disabled = false;
            if (countyTextInput) countyTextInput.disabled = false;
        });
    });

    // Display form validation errors
    function displayFormErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('.help-block.text-danger').forEach(el => {
            el.textContent = '';
        });
        
        // Display new errors
        Object.keys(errors).forEach(field => {
            const errorElement = document.querySelector(`[data-error-for="${field}"]`);
            if (errorElement) {
                errorElement.textContent = errors[field][0];
            }
        });
    }

    // Show message to user
    function showMessage(message, type = 'info') {
        if (!accountSuccess) {
            console.warn('Account success element not found');
            return;
        }
        
        // Remove any existing messages
        const existingAlert = accountSuccess.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        accountSuccess.innerHTML = alertHtml;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                const alert = accountSuccess.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }
    }

    // Clear all messages
    function clearMessages() {
        if (accountSuccess) {
            accountSuccess.innerHTML = '';
        }
        document.querySelectorAll('.help-block.text-danger').forEach(el => {
            el.textContent = '';
        });
    }

    // Update form fields with server data
    function updateFormFields(userData) {
        Object.keys(userData).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input && userData[field] !== undefined) {
                input.value = userData[field] || '';
            }
        });
    }

    // Get CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // Initialize the form
    initializeForm();
});

// Add CSS for spinner animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .postcode-spinner {
        animation: spin .8s linear infinite;
    }
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
`;
document.head.appendChild(style);*/
// Simple, clean JavaScript without complex route handling


document.addEventListener('DOMContentLoaded', function() {
    console.log('Account form initialized');
    
    // Get elements
    const accountForm = document.getElementById('accountForm');
    const countrySelect = document.getElementById('country');
    const countySelect = document.getElementById('county_select');
    const countySelectWrapper = document.getElementById('county_select_wrapper');
    const countyTextInput = document.getElementById('county_text');
    const countyTextWrapper = document.getElementById('county_text_wrapper');
    const postcodeInput = document.getElementById('postcode');
    const postcodeLoader = document.getElementById('postcode_loader');
    const saveBtn = document.getElementById('SaveBtn');
    const accountSuccess = document.getElementById('accountSuccess');

    // Check if elements exist
    if (!accountForm || !countrySelect) {
        console.error('Required form elements not found');
        return;
    }

    // Set form URLs
    const accountUpdateUrl = '/user/account';
    const postcodeLookupBaseUrl = '/user/postcode-lookup/';

    // Initialize form
    function initializeForm() {
        const selectedCountry = countrySelect.value;
        console.log('Initial country:', selectedCountry);
        toggleCountyFields(selectedCountry);
    }

    // Toggle county fields - SIMPLIFIED VERSION
    function toggleCountyFields(country) {
        console.log('Toggling county fields for:', country);
        
        if (!countySelectWrapper || !countyTextWrapper) return;
        
        if (country === 'United States') {
            // Show dropdown, hide text input
            countySelectWrapper.style.display = 'block';
            countyTextWrapper.style.display = 'none';
            
            // Ensure dropdown has name attribute and text input doesn't
            countySelect.setAttribute('name', 'county');
            countyTextInput.removeAttribute('name');
            
            console.log('US mode - using dropdown');
        } else {
            // Show text input, hide dropdown
            countySelectWrapper.style.display = 'none';
            countyTextWrapper.style.display = 'block';
            
            // Ensure text input has name attribute and dropdown doesn't
            countyTextInput.setAttribute('name', 'county');
            countySelect.removeAttribute('name');
            
            console.log('Non-US mode - using text input');
        }
    }

    // Country change handler
    countrySelect.addEventListener('change', function() {
        const selectedCountry = this.value;
        console.log('Country changed to:', selectedCountry);
        toggleCountyFields(selectedCountry);
        
        // Debug: log current field states
        console.log('County select has name:', countySelect.hasAttribute('name'));
        console.log('County text has name:', countyTextInput.hasAttribute('name'));
    });

    // Postcode lookup
    let postcodeTimeout;
    if (postcodeInput && postcodeLoader) {
        postcodeInput.addEventListener('input', function() {
            const postcode = this.value.trim();
            
            clearTimeout(postcodeTimeout);
            if (postcodeLoader) postcodeLoader.style.display = 'none';
            
            if (postcode.length >= 3) {
                if (postcodeLoader) postcodeLoader.style.display = 'block';
                
                postcodeTimeout = setTimeout(() => {
                    lookupPostcode(postcode);
                }, 800);
            }
        });
    }

    // Postcode lookup function
    function lookupPostcode(postcode) {
        if (!postcode) return;
        
        const url = postcodeLookupBaseUrl + encodeURIComponent(postcode);
        console.log('Looking up postcode:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            return response.json();
        })
        .then(data => {
            console.log('Postcode response:', data);
            if (postcodeLoader) postcodeLoader.style.display = 'none';
            
            if (data.success) {
                // Auto-fill city
                const cityInput = document.getElementById('city');
                if (cityInput && !cityInput.value) {
                    cityInput.value = data.data.city || '';
                }
                
                // Auto-fill county/state based on current country
                const selectedCountry = countrySelect.value;
                if (selectedCountry === 'United States') {
                    if (countySelect && data.data.state) {
                        // For US, try to match the state from postcode data
                        const stateName = data.data.state;
                        const option = Array.from(countySelect.options).find(opt => 
                            opt.value.toLowerCase().includes(stateName.toLowerCase()) ||
                            opt.text.toLowerCase().includes(stateName.toLowerCase())
                        );
                        if (option) {
                            countySelect.value = option.value;
                        } else {
                            // If no exact match, just set the value
                            countySelect.value = stateName;
                        }
                        console.log('Set county select value to:', countySelect.value);
                    }
                } else {
                    if (countyTextInput && data.data.state) {
                        countyTextInput.value = data.data.state;
                        console.log('Set county text value to:', countyTextInput.value);
                    }
                }
                showMessage('Address details auto-filled!', 'success');
            } else {
                showMessage(data.message || 'Postcode not found', 'warning');
            }
        })
        .catch(error => {
            console.error('Postcode lookup failed:', error);
            if (postcodeLoader) postcodeLoader.style.display = 'none';
            showMessage('Using demo address data', 'info');
        });
    }

    // Form submission with debug logging
    accountForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Ensure fields are properly configured before submission
        const selectedCountry = countrySelect.value;
        toggleCountyFields(selectedCountry);
        
        const originalHtml = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        
        clearMessages();
        
        const formData = new FormData(this);
        
        // Debug: log what's being submitted
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Country:', countrySelect.value);
        console.log('County select value:', countySelect.value);
        console.log('County select has name:', countySelect.hasAttribute('name'));
        console.log('County text value:', countyTextInput.value);
        console.log('County text has name:', countyTextInput.hasAttribute('name'));
        console.log('FormData entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        console.log('=== END DEBUG ===');
        
        fetch(accountUpdateUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON');
            }
            return response.json();
        })
        .then(data => {
            console.log('Form response:', data);
            
            if (data.success) {
                showMessage('Account updated successfully!', 'success');
                // Update form with server data
                if (data.user) {
                    updateFormFields(data.user);
                }
            } else {
                if (data.errors) {
                    displayFormErrors(data.errors);
                    // Log validation errors
                    console.log('Validation errors:', data.errors);
                } else {
                    showMessage(data.message || 'Update failed', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            showMessage('Error saving changes. Please try again.', 'error');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalHtml;
        });
    });

    // Update form fields with server data
    function updateFormFields(userData) {
        Object.keys(userData).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input && userData[field] !== undefined) {
                input.value = userData[field] || '';
            }
        });
        // Re-initialize form after update
        initializeForm();
    }

    // Display validation errors
    function displayFormErrors(errors) {
        document.querySelectorAll('.help-block.text-danger').forEach(el => {
            el.textContent = '';
        });
        
        Object.keys(errors).forEach(field => {
            const errorElement = document.querySelector(`[data-error-for="${field}"]`);
            if (errorElement) {
                errorElement.textContent = errors[field][0];
            }
        });
    }

    // Show message
    function showMessage(message, type = 'info') {
        if (!accountSuccess) return;
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        accountSuccess.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    // Clear messages
    function clearMessages() {
        if (accountSuccess) accountSuccess.innerHTML = '';
        document.querySelectorAll('.help-block.text-danger').forEach(el => {
            el.textContent = '';
        });
    }

    // Initialize form
    initializeForm();
});

// Change Password
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('changePasswordForm');
  const btn = document.getElementById('changePasswordBtn');
  const box = document.getElementById('changePasswordSuccess');

  function clearErrors() {
    document.querySelectorAll('[data-error-for]').forEach(el => el.innerText = '');
  }
  
  function showErrors(errors) {
    for (const k in errors) {
      const el = document.querySelector('[data-error-for="'+k+'"]');
      if (el) el.innerText = errors[k][0];
    }
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    clearErrors();
    if(box) box.innerHTML = '';

    btn.disabled = true;
    btn.innerText = 'Please wait...';

    const payload = {
      current_password: (document.getElementById('current_password') || {}).value || '',
      password: (document.getElementById('password') || {}).value || '',
      password_confirmation: (document.getElementById('password_confirmation') || {}).value || '',
    };
    
    fetch("/user/change-password", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })
    .then(async res => {
      btn.disabled = false;
      btn.innerText = 'Change Password';

      if(res.ok) {
        const json = await res.json();
        box.innerHTML = '<div class="alert alert-success">'+(json.message || 'Password changed successfully')+'</div>';
        form.reset();
        return;
      }
      if(res.status === 422) {
        const json = await res.json();
        showErrors(json.errors || {});
        return;
      }
      const text = await res.text();
      console.error(text);
      box.innerHTML = '<div class="alert alert-danger">Error changing password. Please try again.</div>';
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerText = 'Change Password';
      console.error(err);
      box.innerHTML = '<div class="alert alert-danger">Server error. Please try again later.</div>';
    });
  });
});
