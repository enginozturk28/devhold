/**
 * DevHold Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Tab sistemi
        $('.devhold-tab-nav a').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this).attr('href');
            
            // Tab navigasyonu güncelle
            $('.devhold-tab-nav a').removeClass('active');
            $(this).addClass('active');
            
            // Tab içeriğini göster
            $('.devhold-tab-content').removeClass('active');
            $(target).addClass('active');
        });
        
        // Artık gerekli değil çünkü detaylı alanları kaldırdık
        
        // Renk seçici
        $('.devhold-color-picker').wpColorPicker();
        
        // Media uploader
        $('.devhold-upload-button').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetInput = $(button.data('target'));
            var previewContainer = $(button.data('preview'));
            
            // Logo mu yoksa arka plan mı belirleme
            var isBackground = targetInput.attr('id') === 'devhold_background_image_url';
            var uploadTitle = isBackground ? 'Arka Plan Görseli Seçin' : 'Logo Seçin';
            var uploadButton = isBackground ? 'Bu Görseli Kullan' : 'Bu Logoyu Kullan';
            var altText = isBackground ? 'Arka Plan' : 'Logo';
            
            // Media uploader oluştur
            var mediaUploader = wp.media({
                title: uploadTitle,
                button: {
                    text: uploadButton
                },
                multiple: false
            });
            
            // Resim seçildiğinde
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                previewContainer.html('<img src="' + attachment.url + '" alt="' + altText + '">');
                button.siblings('.devhold-remove-button').show();
            });
            
            mediaUploader.open();
        });
        
        // Logo kaldır
        $('.devhold-remove-button').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetInput = $(button.data('target'));
            var previewContainer = $(button.data('preview'));
            
            targetInput.val('');
            previewContainer.empty();
            button.hide();
        });
        
        // Sosyal medya ekle
        var socialIndex = $('.devhold-social-item').length;
        
        $('#devhold-add-social').on('click', function(e) {
            e.preventDefault();
            
            var newItem = '<div class="devhold-social-item">' +
                '<select name="devhold_options[social_links][' + socialIndex + '][platform]" class="devhold-social-platform">' +
                '<option value="">Platform Seçin</option>' +
                '<option value="facebook">Facebook</option>' +
                '<option value="twitter">Twitter</option>' +
                '<option value="instagram">Instagram</option>' +
                '<option value="linkedin">LinkedIn</option>' +
                '<option value="youtube">YouTube</option>' +
                '<option value="github">GitHub</option>' +
                '</select>' +
                '<input type="url" name="devhold_options[social_links][' + socialIndex + '][url]" placeholder="URL" class="regular-text">' +
                '<button type="button" class="button devhold-remove-social">Kaldır</button>' +
                '</div>';
            
            $('#devhold-social-container').append(newItem);
            socialIndex++;
        });
        
        // Sosyal medya kaldır
        $(document).on('click', '.devhold-remove-social', function(e) {
            e.preventDefault();
            $(this).closest('.devhold-social-item').fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Hızlı durum değiştir
        $('#devhold-quick-toggle').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            button.prop('disabled', true);
            
            $.ajax({
                url: devhold.ajax_url,
                type: 'POST',
                data: {
                    action: 'devhold_toggle_status',
                    nonce: devhold.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                        button.prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Bağlantı hatası. Lütfen tekrar deneyin.');
                    button.prop('disabled', false);
                }
            });
        });
        
    });
    
})(jQuery);

// Admin bar için global fonksiyon
function devholdToggleStatus() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'devhold_toggle_status',
            nonce: devhold.nonce
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        },
        error: function() {
            alert('Bağlantı hatası. Lütfen tekrar deneyin.');
        }
    });
}