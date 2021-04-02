<html>
<head>
    <!-- Load the jQuery JS library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- Custom JS script -->
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            function trans(html) {
                let labels = html.match(/(?<=\[\[)\w+.\w+(?=\]\])/g);

                for (let label in labels) {
                    let translation = labels[label].split('.').reduce((t, i) => t[i] || null, window.translations);
                    html = html.replace(`[[${labels[label]}]]`, translation);
                }

                return html;
            }

            function get_total_price() {
                let sum = 0;
                if ($('.cart .price').length > 0) {
                    $('.cart .price').each(function () {
                        sum += parseFloat($(this).text());
                    });
                    $('#totalPrice span').text(sum);
                } else {
                    $('#totalPrice').remove();
                }
            }

            function renderList(products, totalPrice = null) {
                html = [
                    '<tr>',
                    '<th>[[labels.title]]</th>',
                    '<th>[[labels.description]]</th>',
                    '<th>[[labels.price]]</th>',
                    '</tr>'
                ].join('');

                $.each(products, function (key, product) {
                    let btnLabel = totalPrice ? '[[labels.remove]]' : '[[labels.add]]';
                    html += [
                        '<tr>',
                        '<td>' + product.title + '</td>',
                        '<td>' + product.description + '</td>',
                        '<td class="price">' + product.price + '</td>',
                        '<td><button class="actionBtn" value="' + product.id + '">' + btnLabel + '</button></td>',
                        '</tr>'
                    ].join('');
                });

                if (totalPrice) {
                    html += [
                        '<tr>',
                        '<td id="totalPrice">[[labels.total_price]]: <span>' + totalPrice + '</span></td>',
                        '</tr>'
                    ].join('');
                }

                return html;
            }

            window.onhashchange = function () {
                $('.page').hide();

                switch (window.location.hash) {
                    case '#cart':
                        $('.cart').html(trans($('.cart').html())).show();

                        $.ajax('{{ route('cart.show') }}', {
                            dataType: 'json',
                            success: function (response) {
                                $('.cart .list, #checkout').show();
                                $('#emptyCart').hide();
                                if (!response['cartProducts'].length) {
                                    $('.cart .list, #checkout').hide();
                                    $('#emptyCart').show();
                                }
                                $('.cart .list').html(trans(renderList(response['cartProducts'], response['totalPrice'])));

                                $('.actionBtn').on('click', function (e) {
                                    const id = $(this).val();
                                    let url = '{{ route('cart.destroy', ':id') }}';
                                    url = url.replace(':id', id);
                                    $.ajax({
                                        url: url,
                                        type: 'DELETE',
                                        success: function () {
                                            $(e.target).parent().parent().remove();
                                            get_total_price();
                                        }
                                    });
                                });
                            }
                        });

                        $('#checkout').on('submit', function (e) {
                            e.preventDefault();
                            $.ajax('{{ route('order.store') }}', {
                                type: 'POST',
                                data: {
                                    name: $('#name').val(),
                                    details: $('#details').val(),
                                    comments: $('#comments').val(),
                                    totalPrice: $('#totalPrice span').text(),
                                },
                                success: function (response) {
                                    if (response['statusCode'] === 200) {
                                        window.location.hash = '/';
                                    }
                                },
                                error: function (response) {
                                    $('form span').hide();
                                    let errors = response.responseJSON.errors;
                                    for (let error in errors) {
                                        $('span.' + error).text(errors[error]).show();
                                    }
                                }
                            });
                        });
                        break;
                    default:
                        $('.index').html(trans($('.index').html())).show();

                        $.ajax('{{ route('product.index') }}', {
                            dataType: 'json',
                            success: function (response) {
                                $('.index .list').html(trans(renderList(response)));

                                $('.actionBtn').on('click', function (e) {
                                    const id = $(this).val();
                                    let url = '{{ route('cart.store', ':id') }}';
                                    url = url.replace(':id', id);
                                    $.ajax({
                                        url: url,
                                        type: 'POST',
                                        success: function () {
                                            $(e.target).parent().parent().remove();
                                        }
                                    });
                                });
                            }
                        });
                        break;
                }
            }
            window.onhashchange();
        });
    </script>
</head>
<body>
<!-- The index page -->
<div class="page index">
    <table class="list" style="border: 1px solid black"></table>

    <a href="#cart" class="button">[[labels.go_to_cart]]</a>
</div>

<!-- The cart page -->
<div class="page cart">
    <table class="list" style="border: 1px solid black"></table>

    <p id="emptyCart" style="display: none">[[labels.empty_cart]]</p>
    <form id="checkout" class="cart-form">
        <input
            id="name"
            type="text"
            name="name"
            placeholder="[[labels.name]]"
        >
        <span class="error name" style="display: none"></span>
        <br><br>
        <input
            id="details"
            type="text"
            name="details"
            placeholder="[[labels.contact_details]]"
        >
        <span class="error details" style="display: none"></span>
        <br><br>
        <textarea
            id="comments"
            rows="4"
            name="comments"
            placeholder="[[labels.comments]]"></textarea>
        <br><br>
        <input type="submit" value="[[labels.checkout]]">
    </form>

    <a href="#" class="button">[[labels.go_to_index]]</a>
</div>
<script>
    window.translations = {!! Cache::get('translations') !!};
</script>
</body>
</html>
