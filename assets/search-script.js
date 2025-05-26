
jQuery(function ($) {
    const $input = $('.custom-search-form .search-field');
    const $category = $('.custom-search-form .category-dropdown');

    $input.autocomplete({
        source: function (request, response) {
            $.ajax({
                url: wcps_ajax_object.ajax_url,
                dataType: "json",
                data: {
                    action: "wcps_live_search",
                    term: request.term,
                    category: $category.val()
                },
                success: function (data) {
                    if (data.length) {
                        response(data);
                    } else {
                        response([{ label: "No products found based on your search...", value: "" }]);
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            if (ui.item.value) {
                window.location.href = ui.item.value;
            }
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        if (!item.value) {
            return $("<li>")
                .append(`<div style="padding:10px;color:#999;text-align:center;">${item.label}</div>`)
                .appendTo(ul);
        }

        const image = item.image ? `<img src="${item.image}" alt="${item.label}" style="width:40px;height:auto;margin-right:10px;border-radius:4px;">` : '';
        const price = item.price ? `<span class="search-price">${item.price}</span>` : '';
        return $("<li>")
            .append(`<div style="display:flex;align-items:center;padding:8px;">${image}<div><strong>${item.label}</strong><br>${price}</div></div>`)
            .appendTo(ul);
    };
});

