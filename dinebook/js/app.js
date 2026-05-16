// js/app.js — jQuery interactions for DineBook (10 distinct uses)

$(document).ready(function () {

    // 1. Form field highlight on focus
    $('input, select, textarea').on('focus', function () {
        $(this).addClass('field-active');
    }).on('blur', function () {
        $(this).removeClass('field-active');
    });

    // 2. Confirm before delete submission
    $('#delete-confirm-form').on('submit', function (e) {
        if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
            e.preventDefault();
        }
    });

    // 3. Character counter for all textareas
    $('textarea').each(function () {
        var $ta = $(this);
        if (!$ta.next('.char-count').length) {
            $ta.after('<small class="char-count"></small>');
        }
        var max = 300, len = $ta.val().length;
        $ta.next('.char-count').text((max - len) + ' characters remaining');
    });
    $('textarea').on('input', function () {
        var max = 300, len = $(this).val().length;
        $(this).next('.char-count').text((max - len) + ' characters remaining');
    });

    // 4. Dynamic party size warning
    $('#party_size').on('change', function () {
        if (parseInt($(this).val()) > 8) {
            $('#party-warning').removeClass('d-none').show();
        } else {
            $('#party-warning').addClass('d-none').hide();
        }
    });

    // 5. Fade-in animation on page load
    $('main, .container').hide().fadeIn(400);

    // 6. Highlight table rows on hover
    $('table tbody tr').on('mouseenter', function () {
        $(this).addClass('table-hover-row');
    }).on('mouseleave', function () {
        $(this).removeClass('table-hover-row');
    });

    // 7. Auto-hide success/error messages after 4 seconds
    setTimeout(function () {
        $('.success-msg, .alert-success').fadeOut(600);
    }, 4000);

    // 8. Toggle extra details section in booking form
    $('#toggle-details').on('click', function () {
        $('#extra-details').slideToggle(300);
        $(this).text($(this).text() === 'Show details' ? 'Hide details' : 'Show details');
    });

    // 9. Live search filter on report tables (client-side)
    $('#table-filter').on('keyup', function () {
        var val = $(this).val().toLowerCase();
        $('table tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // 10. Form reset button with confirmation
    $('.btn-reset').on('click', function (e) {
        if (!confirm('Clear all fields?')) {
            e.preventDefault();
        }
    });

});
