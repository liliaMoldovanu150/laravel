<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script type="text/javascript">
        const config = {
            'csrfToken': '{{ csrf_token() }}',
            'translations': {!! Cache::get('translations') !!},
            'productIndex': '{{ route('product.index') }}',
            'cartStore': '{{ route('cart.store', ':id') }}',
            'cartDestroy': '{{ route('cart.destroy', ':id') }}',
            'cartShow': '{{ route('cart.show') }}',
            'orderStore': '{{ route('order.store') }}',
            'login': '{{ route('login') }}',
            'productDestroy': '{{ route('product.destroy', ':id') }}',
            'productStore': '{{ route('product.store') }}',
            'productDisplay': '{{ route('product.display') }}',
            'productCreate': '{{ route('product.create') }}',
            'productEdit': '{{ route('product.edit', ':id') }}',
            'productUpdate': '{{ route('product.update', ':id') }}',
            'orderIndex': '{{ route('order.index') }}',
            'orderShow': '{{ route('order.show', ':id') }}',
            'logout': '{{ route('logout') }}'
        }

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                }
            });

            function trans(html) {
                let labels = html.match(/(?<=\[\[)\w+.\w+(?=\]\])/g);

                for (let label in labels) {
                    let translation = labels[label].split('.').reduce((t, i) => t[i] || null, config.translations);
                    html = html.replace(`[[${labels[label]}]]`, translation);
                }

                return html;
            }

            function renderList(products, totalPrice = null, adminMode = null) {
                let html = [
                    '<tr>',
                    '<th>[[labels.product_image]]</th>',
                    '<th>[[labels.title]]</th>',
                    '<th>[[labels.description]]</th>',
                    '<th>[[labels.price]]</th>',
                    '</tr>'
                ].join('');

                $.each(products, function (key, product) {
                    if (!adminMode) {
                        html += renderRow(product, totalPrice);
                    } else {
                        html += renderAdminRow(product);
                    }
                });

                if (!adminMode && totalPrice) {
                    html += renderTotalPrice(totalPrice);
                }

                return html;
            }

            function renderRow(product, totalPrice) {
                let btnLabel = totalPrice ? '[[labels.remove]]' : '[[labels.add]]';
                let row = [
                    '<tr>',
                    '<td><img style="width: 100px; height: 100px; object-fit: cover" src="./images/'
                    + product.image_url
                    + '" alt="[[labels.product_image]]"></td>',
                    '<td>' + product.title + '</td>',
                    '<td>' + product.description + '</td>',
                    '<td class="price">' + product.price + '</td>',
                    '<td><button class="actionBtn" value="' + product.id + '">' + btnLabel + '</button></td>',
                    '</tr>'
                ].join('');

                return row;
            }

            function renderAdminRow(product) {
                let adminRow = [
                    '<tr>',
                    '<td><img style="width: 100px; height: 100px; object-fit: cover" src="./images/'
                    + product.image_url
                    + '" alt="[[labels.product_image]]"></td>',
                    '<td>' + product.title + '</td>',
                    '<td>' + product.description + '</td>',
                    '<td class="price">' + product.price + '</td>',
                    '<td><a href class="editProduct" data-id="' + product.id + '">[[labels.edit]]</a></td>',
                    '<td><a href class="deleteProduct" data-id="' + product.id + '">[[labels.delete]]</a></td>',
                    '</tr>'
                ].join('');

                return adminRow;
            }

            function renderTotalPrice(totalPrice) {
                let totalPriceRow = [
                    '<tr>',
                    '<td id="totalPrice" colspan="2">[[labels.total_price]]: <span>' + totalPrice + '</span></td>',
                    '</tr>'
                ].join('');

                return totalPriceRow;
            }

            function renderOrders(orders) {
                let orderRow = [
                    '<tr>',
                    '<th>[[labels.id]]</th>',
                    '<th>[[labels.order_total]]</th>',
                    '</tr>'
                ].join('');

                $.each(orders, function (key, order) {
                    orderRow += [
                        '<tr>',
                        '<td><a href>' + order.id + '</a></td>',
                        '<td>' + order.total_price + '</td>',
                        '</tr>'
                    ].join('');
                });

                return orderRow;
            }

            function getTotalPrice() {
                let sum = 0;
                if ($('.cart .price').length > 0) {
                    $('.cart .price').each(function () {
                        sum += parseFloat($(this).text());
                    });
                }

                return sum;
            }

            window.onhashchange = function () {
                $('.page').hide();
                const regexEdit = /(?<=^#products\/)\d+(?=\/edit$)/g;
                const editProductId = window.location.hash.match(regexEdit)
                    ? window.location.hash.match(regexEdit)[0] : null;

                const regexOrder = /(?<=^#orders\/)\d+$/g;
                const orderId = window.location.hash.match(regexOrder)
                    ? window.location.hash.match(regexOrder)[0] : null;

                switch (window.location.hash) {
                    case '#cart':
                        $('.cart').html(trans($('.cart').html())).show();

                        $.ajax(config.cartShow, {
                            dataType: 'json',
                            success: function (response) {
                                if (!response['cartProducts'].length) {
                                    $('.cart .list, #checkoutForm').hide();
                                    $('#emptyCart').show();
                                } else {
                                    $('.cart .list, #checkoutForm').show();
                                    $('#emptyCart').hide();
                                }
                                $('.cart .list').html(trans(renderList(response['cartProducts'], response['totalPrice'])));

                                $('.cart .list').on('click', '.actionBtn', function (e) {
                                    const id = $(this).val();
                                    let url = config.cartDestroy.replace(':id', id);
                                    $.ajax(url, {
                                        type: 'DELETE',
                                        dataType: 'json',
                                        success: function () {
                                            $(e.target).parents('tr').remove();
                                            let totalPrice = getTotalPrice();
                                            if (totalPrice) {
                                                $('#totalPrice span').text(totalPrice);
                                            } else {
                                                $('#totalPrice').remove();
                                                $('.cart .list, #checkoutForm').hide();
                                                $('#emptyCart').show();
                                            }
                                        }
                                    });
                                });
                            }
                        });

                        $('#checkoutForm').on('submit', function (e) {
                            $('form span').hide();
                            e.preventDefault();
                            const formData = new FormData(this);
                            formData.append('totalPrice', $('#totalPrice span').text())
                            $.ajax(config.orderStore, {
                                type: 'POST',
                                dataType: 'json',
                                contentType: false,
                                processData: false,
                                data: formData,
                                success: function () {
                                    window.location.hash = '#';
                                },
                                error: function (response) {
                                    let errors = response.responseJSON.errors;
                                    for (let error in errors) {
                                        $('span.' + error).text(errors[error]).show();
                                    }
                                }
                            });
                        });
                        break;
                    case '#login':
                        $('.login').html(trans($('.login').html())).show();

                        $('#loginForm').on('submit', function (e) {
                            $('form span').hide();
                            e.preventDefault();
                            const formData = new FormData(this);
                            $.ajax(config.login, {
                                type: 'POST',
                                dataType: 'json',
                                contentType: false,
                                processData: false,
                                data: formData,
                                success: function () {
                                    window.location.hash = '#';
                                },
                                error: function (response) {
                                    let errors = response.responseJSON.errors;
                                    for (let error in errors) {
                                        $('span.' + error).text(errors[error]).show();
                                    }
                                }
                            });
                        });
                        break;
                    case '#products':
                        $.ajax(config.productDisplay, {
                            dataType: 'json',
                            success: function (response) {
                                $('.products').html(trans($('.products').html())).show();
                                $('.products .list').html(trans(renderList(response, null, 'adminMode')));

                                $('.products .list').on('click', 'a', function (e) {
                                    e.preventDefault();
                                    const id = $(this).attr('data-id');
                                    if ($(e.target).attr('class') === 'editProduct') {
                                        window.location.hash = '#products/' + id + '/edit';
                                    } else {
                                        let url = config.productDestroy.replace(':id', id);
                                        $.ajax(url, {
                                            type: 'DELETE',
                                            dataType: 'json',
                                            success: function () {
                                                $(e.target).parents('tr').remove();
                                            }
                                        });
                                    }
                                });

                                $('#logout').on('click', function (e) {
                                    e.preventDefault();
                                    $.ajax(config.logout, {
                                        type: 'POST',
                                        dataType: 'json',
                                        success: function () {
                                            window.location.hash = '#';
                                        },
                                    });
                                })
                            },
                            error: function () {
                                window.location.hash = '#';
                            }
                        });
                        break;
                    case '#products/' + editProductId + '/edit':
                        let url = config.productEdit.replace(':id', editProductId);
                        $.ajax(url, {
                            dataType: 'json',
                            success: function (response) {
                                $('.product').html(trans($('.product').html())).show();
                                $('#productForm span').hide();
                                $('#title').val(response.title);
                                $('#description').val(response.description);
                                $('#price').val(response.price);

                                $('#productForm').on('submit', function (e) {
                                    url = config.productUpdate.replace(':id', editProductId);
                                    const formData = new FormData(this);
                                    formData.append('_method', 'PUT');
                                    e.preventDefault();
                                    $.ajax(url, {
                                        type: 'POST',
                                        dataType: 'json',
                                        contentType: false,
                                        processData: false,
                                        data: formData,
                                        success: function () {
                                            window.location.hash = '#products';
                                        },
                                        error: function (response) {
                                            let errors = response.responseJSON.errors;
                                            for (let error in errors) {
                                                $('span.' + error).text(errors[error]).show();
                                            }
                                        }
                                    });
                                })
                            },
                            error: function () {
                                window.location.hash = '#';
                            }
                        });
                        break;
                    case '#products/create':
                        $.ajax(config.productCreate, {
                            dataType: 'json',
                            success: function () {
                                $('.product').html(trans($('.product').html())).show();

                                $('#productForm').on('submit', function (e) {
                                    $('#productForm span').hide();
                                    e.preventDefault();
                                    const formData = new FormData(this);
                                    $.ajax(config.productStore, {
                                        type: 'POST',
                                        dataType: 'json',
                                        contentType: false,
                                        processData: false,
                                        data: formData,
                                        success: function () {
                                            window.location.hash = '#products';
                                        },
                                        error: function (response) {
                                            let errors = response.responseJSON.errors;
                                            for (let error in errors) {
                                                $('span.' + error).text(errors[error]).show();
                                            }
                                        }
                                    });
                                })
                            },
                            error: function () {
                                window.location.hash = '#';
                            }
                        });
                        break;
                    case '#orders':
                        $('.orders').html(trans($('.orders').html())).show();

                        $.ajax(config.orderIndex, {
                            dataType: 'json',
                            success: function (response) {
                                $('.orders .ordersList').html(trans(renderOrders(response)));

                                $('.orders .ordersList').on('click', 'a', function (e) {
                                    e.preventDefault();
                                    const id = $(this).text();
                                    window.location.hash = '#orders/' + id;
                                })
                            }
                        });
                        break;
                    case '#orders/' + orderId:
                        let orderShowUrl = config.orderShow.replace(':id', orderId);
                        $.ajax(orderShowUrl, {
                            dataType: 'json',
                            success: function (response) {
                                $('.order').html(trans($('.order').html())).show();
                            }
                        });
                        break;
                    default:
                        $('.index').html(trans($('.index').html())).show();

                        $.ajax(config.productIndex, {
                            dataType: 'json',
                            success: function (response) {
                                if (!response.length) {
                                    $('.index .list').hide();
                                    $('#allAdded').show();
                                } else {
                                    $('.index .list').html(trans(renderList(response)));
                                }

                                $('.index .list').on('click', '.actionBtn', function (e) {
                                    const id = $(this).val();
                                    let url = config.cartStore.replace(':id', id);
                                    $.ajax(url, {
                                        type: 'POST',
                                        dataType: 'json',
                                        success: function () {
                                            $(e.target).parents('tr').remove();
                                        }
                                    });
                                })
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

<div class="page index">
    <table class="list" style="border: 1px solid black"></table>

    <p id="allAdded" style="display: none">[[labels.all_added]]</p>

    <a href="#cart" class="button">[[labels.go_to_cart]]</a>
</div>

<div class="page cart">
    <table class="list" style="border: 1px solid black"></table>

    <p id="emptyCart" style="display: none">[[labels.empty_cart]]</p>
    <form id="checkoutForm">
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

<div class="page login">
    <form id="loginForm">
        <input
            id="email"
            type="email"
            name="email"
            placeholder="[[labels.email]]"
        >
        <br>
        <span class="error email" style="display: none"></span>
        <br>
        <input
            id="password"
            type="password"
            name="password"
            placeholder="[[labels.password]]"
        >
        <br>
        <span class="error password" style="display: none"></span>
        <br>
        <input type="submit" value="[[labels.login]]">
    </form>
</div>

<div class="page products">
    <table class="list" style="border: 1px solid black"></table>

    <a id="addProduct" href="#products/create">[[labels.add]]</a>
    <a id="logout" href>[[labels.logout]]</a>
</div>

<div class="page product">
    <form id="productForm" enctype="multipart/form-data">
        <input
            id="title"
            type="text"
            name="title"
            placeholder="[[labels.title]]"
        >
        <span class="error title"></span>
        <br><br>
        <textarea
            id="description"
            rows="5"
            cols="20"
            type="text"
            name="description"
            placeholder="[[labels.description]]"
        ></textarea>
        <span class="error description"></span>
        <br><br>
        <input
            id="price"
            type="number"
            name="price"
            min="0.00"
            step="0.01"
            placeholder="[[labels.price]]"
        >
        <span class="error price"></span>
        <br><br>
        <input
            id="image"
            type="file"
            name="image"
        >
        <span class="error image"></span>
        <br><br>
        <input type="submit" value="[[labels.save]]">
    </form>
    <a href="#products">[[labels.products]]</a>
</div>

<div class="page orders">
    <h1 class="heading">[[labels.orders]]</h1>
    <table class="ordersList" style="border: 1px solid black"></table>
</div>

<div class="page order">
    <h1 class="heading">[[labels.order]]</h1>
</div>
</body>
</html>
