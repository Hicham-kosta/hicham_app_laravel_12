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