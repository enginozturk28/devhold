<?php
/**
 * DevHold Admin Settings Page
 *
 * @package DevHold
 */

// Güvenlik kontrolü
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Mevcut ayarları al
$options = get_option( 'devhold_options' );

// Varsayılan değerleri ekle (eğer yoksa)
$default_options = array(
    'enabled' => false,
    'title' => __( 'Geliştirme Aşamasında', 'devhold' ),
    'message' => __( 'Sitemiz şu anda geliştirme aşamasındadır. Lütfen daha sonra tekrar ziyaret ediniz.', 'devhold' ),
    'countdown_enabled' => false,
    'countdown_date' => '',
    'social_links' => array(),
    'custom_css' => '',
    'logo_url' => '',
    'background_color' => '#667eea',
    'text_color' => '#ffffff',
    'bypass_roles' => array( 'administrator' ),
    'design_style' => 'minimal',
    'background_image' => '',
    'subtitle' => __( 'Geliştirme Aşamasında', 'devhold' )
);

// Eksik değerleri varsayılanlarla doldur
$options = wp_parse_args( $options, $default_options );

// WordPress rolleri
$wp_roles = wp_roles();
$roles = $wp_roles->get_names();
?>

<div class="wrap">
    <h1><?php esc_html_e( 'DevHold Bakım Modu Ayarları', 'devhold' ); ?></h1>
    
    <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Ayarlar başarıyla kaydedildi.', 'devhold' ); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="devhold-status-bar">
        <h2>
            <?php esc_html_e( 'Bakım Modu Durumu:', 'devhold' ); ?>
            <span class="devhold-status <?php echo ! empty( $options['enabled'] ) ? 'active' : 'inactive'; ?>">
                <?php echo ! empty( $options['enabled'] ) ? esc_html__( 'Aktif', 'devhold' ) : esc_html__( 'Pasif', 'devhold' ); ?>
            </span>
        </h2>
        <button type="button" class="button button-primary devhold-toggle-button" id="devhold-quick-toggle">
            <?php echo ! empty( $options['enabled'] ) ? esc_html__( 'Devre Dışı Bırak', 'devhold' ) : esc_html__( 'Etkinleştir', 'devhold' ); ?>
        </button>
    </div>
    
    <form method="post" action="options.php" class="devhold-settings-form">
        <?php settings_fields( 'devhold_settings' ); ?>
        
        <div class="devhold-tabs">
            <ul class="devhold-tab-nav">
                <li><a href="#general" class="active"><?php esc_html_e( 'Genel', 'devhold' ); ?></a></li>
                <li><a href="#design"><?php esc_html_e( 'Tasarım', 'devhold' ); ?></a></li>
                <li><a href="#countdown"><?php esc_html_e( 'Geri Sayım', 'devhold' ); ?></a></li>
                <li><a href="#social"><?php esc_html_e( 'Sosyal Medya', 'devhold' ); ?></a></li>
                <li><a href="#access"><?php esc_html_e( 'Erişim Kontrolü', 'devhold' ); ?></a></li>
            </ul>
            
            <!-- Genel Ayarlar -->
            <div id="general" class="devhold-tab-content active">
                <h3><?php esc_html_e( 'Genel Ayarlar', 'devhold' ); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="devhold_enabled"><?php esc_html_e( 'Bakım Modu', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <label class="devhold-switch">
                                <input type="checkbox" name="devhold_options[enabled]" id="devhold_enabled" value="1" <?php checked( ! empty( $options['enabled'] ) ); ?>>
                                <span class="devhold-slider"></span>
                            </label>
                            <p class="description"><?php esc_html_e( 'Bakım modunu etkinleştir veya devre dışı bırak.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_title"><?php esc_html_e( 'Başlık', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="devhold_options[title]" id="devhold_title" value="<?php echo esc_attr( $options['title'] ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Bakım sayfasında gösterilecek başlık.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_subtitle"><?php esc_html_e( 'Alt Başlık', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="devhold_options[subtitle]" id="devhold_subtitle" value="<?php echo esc_attr( $options['subtitle'] ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Sayfada gösterilecek ana başlık.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_message"><?php esc_html_e( 'Mesaj', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_editor( 
                                $options['message'], 
                                'devhold_message', 
                                array(
                                    'textarea_name' => 'devhold_options[message]',
                                    'textarea_rows' => 5,
                                    'media_buttons' => false,
                                    'teeny' => true
                                )
                            );
                            ?>
                            <p class="description"><?php esc_html_e( 'Ziyaretçilere gösterilecek açıklama mesajı.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Tasarım Ayarları -->
            <div id="design" class="devhold-tab-content">
                <h3><?php esc_html_e( 'Tasarım Ayarları', 'devhold' ); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="devhold_design_style"><?php esc_html_e( 'Tasarım Stili', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <div class="devhold-design-options">
                                <label class="devhold-design-option">
                                    <input type="radio" name="devhold_options[design_style]" value="minimal" <?php checked( $options['design_style'], 'minimal' ); ?>>
                                    <div class="devhold-design-preview">
                                        <div class="preview-minimal">
                                            <div class="preview-header">🔧</div>
                                            <div class="preview-title">Minimal</div>
                                            <div class="preview-text">Kısa başlık ve açıklama</div>
                                        </div>
                                    </div>
                                    <span class="design-name"><?php esc_html_e( 'Minimal', 'devhold' ); ?></span>
                                </label>
                                
                                <label class="devhold-design-option">
                                    <input type="radio" name="devhold_options[design_style]" value="detailed" <?php checked( $options['design_style'], 'detailed' ); ?>>
                                    <div class="devhold-design-preview">
                                        <div class="preview-detailed">
                                            <div class="preview-header">🔧</div>
                                            <div class="preview-title">Genişletilmiş</div>
                                            <div class="preview-text">Daha geniş alan</div>
                                        </div>
                                    </div>
                                    <span class="design-name"><?php esc_html_e( 'Genişletilmiş', 'devhold' ); ?></span>
                                </label>
                            </div>
                            <p class="description"><?php esc_html_e( 'Bakım sayfası için tasarım stilini seçin.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_logo"><?php esc_html_e( 'Logo', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <div class="devhold-media-upload">
                                <input type="hidden" name="devhold_options[logo_url]" id="devhold_logo_url" value="<?php echo esc_attr( $options['logo_url'] ); ?>">
                                <div class="devhold-logo-preview">
                                    <?php if ( ! empty( $options['logo_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $options['logo_url'] ); ?>" alt="Logo">
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button devhold-upload-button" data-target="#devhold_logo_url" data-preview=".devhold-logo-preview">
                                    <?php esc_html_e( 'Logo Seç', 'devhold' ); ?>
                                </button>
                                <button type="button" class="button devhold-remove-button" data-target="#devhold_logo_url" data-preview=".devhold-logo-preview" <?php echo empty( $options['logo_url'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php esc_html_e( 'Kaldır', 'devhold' ); ?>
                                </button>
                            </div>
                            <p class="description"><?php esc_html_e( 'Bakım sayfasında gösterilecek logo. Boş bırakılırsa varsayılan ikon gösterilir.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_background_color"><?php esc_html_e( 'Arka Plan Rengi', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="devhold_options[background_color]" id="devhold_background_color" value="<?php echo esc_attr( $options['background_color'] ); ?>" class="devhold-color-picker">
                            <p class="description"><?php esc_html_e( 'Gradient arka plan için başlangıç rengi.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_text_color"><?php esc_html_e( 'Metin Rengi', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="devhold_options[text_color]" id="devhold_text_color" value="<?php echo esc_attr( $options['text_color'] ); ?>" class="devhold-color-picker">
                            <p class="description"><?php esc_html_e( 'Başlık ve metin rengi.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_background_image"><?php esc_html_e( 'Arka Plan Görseli', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <div class="devhold-media-upload">
                                <input type="hidden" name="devhold_options[background_image]" id="devhold_background_image_url" value="<?php echo esc_attr( $options['background_image'] ); ?>">
                                <div class="devhold-background-preview">
                                    <?php if ( ! empty( $options['background_image'] ) ) : ?>
                                        <img src="<?php echo esc_url( $options['background_image'] ); ?>" alt="Arka Plan">
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button devhold-upload-button" data-target="#devhold_background_image_url" data-preview=".devhold-background-preview">
                                    <?php esc_html_e( 'Arka Plan Seç', 'devhold' ); ?>
                                </button>
                                <button type="button" class="button devhold-remove-button" data-target="#devhold_background_image_url" data-preview=".devhold-background-preview" <?php echo empty( $options['background_image'] ) ? 'style="display:none;"' : ''; ?>>
                                    <?php esc_html_e( 'Kaldır', 'devhold' ); ?>
                                </button>
                            </div>
                            <p class="description"><?php esc_html_e( 'Arka plan için görsel seçin. Boş bırakılırsa gradient arka plan kullanılır.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_custom_css"><?php esc_html_e( 'Özel CSS', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <textarea name="devhold_options[custom_css]" id="devhold_custom_css" rows="10" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Bakım sayfasına eklemek istediğiniz özel CSS kodları.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Geri Sayım -->
            <div id="countdown" class="devhold-tab-content">
                <h3><?php esc_html_e( 'Geri Sayım Ayarları', 'devhold' ); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="devhold_countdown_enabled"><?php esc_html_e( 'Geri Sayım', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <label class="devhold-switch">
                                <input type="checkbox" name="devhold_options[countdown_enabled]" id="devhold_countdown_enabled" value="1" <?php checked( ! empty( $options['countdown_enabled'] ) ); ?>>
                                <span class="devhold-slider"></span>
                            </label>
                            <p class="description"><?php esc_html_e( 'Geri sayım sayacını göster.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="devhold_countdown_date"><?php esc_html_e( 'Bitiş Tarihi', 'devhold' ); ?></label>
                        </th>
                        <td>
                            <input type="datetime-local" name="devhold_options[countdown_date]" id="devhold_countdown_date" value="<?php echo esc_attr( $options['countdown_date'] ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Geri sayımın biteceği tarih ve saat.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Sosyal Medya -->
            <div id="social" class="devhold-tab-content">
                <h3><?php esc_html_e( 'Sosyal Medya Linkleri', 'devhold' ); ?></h3>
                
                <div class="devhold-social-links">
                    <div id="devhold-social-container">
                        <?php
                        if ( ! empty( $options['social_links'] ) ) {
                            foreach ( $options['social_links'] as $index => $link ) {
                                ?>
                                <div class="devhold-social-item">
                                    <select name="devhold_options[social_links][<?php echo $index; ?>][platform]" class="devhold-social-platform">
                                        <option value=""><?php esc_html_e( 'Platform Seçin', 'devhold' ); ?></option>
                                        <option value="facebook" <?php selected( $link['platform'], 'facebook' ); ?>>Facebook</option>
                                        <option value="twitter" <?php selected( $link['platform'], 'twitter' ); ?>>Twitter</option>
                                        <option value="instagram" <?php selected( $link['platform'], 'instagram' ); ?>>Instagram</option>
                                        <option value="linkedin" <?php selected( $link['platform'], 'linkedin' ); ?>>LinkedIn</option>
                                        <option value="youtube" <?php selected( $link['platform'], 'youtube' ); ?>>YouTube</option>
                                        <option value="github" <?php selected( $link['platform'], 'github' ); ?>>GitHub</option>
                                    </select>
                                    <input type="url" name="devhold_options[social_links][<?php echo $index; ?>][url]" value="<?php echo esc_url( $link['url'] ); ?>" placeholder="<?php esc_attr_e( 'URL', 'devhold' ); ?>" class="regular-text">
                                    <button type="button" class="button devhold-remove-social"><?php esc_html_e( 'Kaldır', 'devhold' ); ?></button>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <button type="button" class="button button-secondary" id="devhold-add-social">
                        <?php esc_html_e( 'Sosyal Medya Ekle', 'devhold' ); ?>
                    </button>
                </div>
            </div>
            
            <!-- Erişim Kontrolü -->
            <div id="access" class="devhold-tab-content">
                <h3><?php esc_html_e( 'Erişim Kontrolü', 'devhold' ); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Bakım Modunu Atlayabilecek Roller', 'devhold' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <?php foreach ( $roles as $role_key => $role_name ) : ?>
                                    <label>
                                        <input type="checkbox" name="devhold_options[bypass_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, (array) $options['bypass_roles'] ) ); ?>>
                                        <?php echo esc_html( translate_user_role( $role_name ) ); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            </fieldset>
                            <p class="description"><?php esc_html_e( 'Seçilen rollere sahip kullanıcılar bakım modunu görmeden siteye erişebilir.', 'devhold' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="devhold-footer">
        <p>
            <?php esc_html_e( 'DevHold', 'devhold' ); ?> v<?php echo DEVHOLD_VERSION; ?> | 
            <?php esc_html_e( 'Geliştirici:', 'devhold' ); ?> <a href="https://enginozturk.tr" target="_blank">Engin ÖZTÜRK & Claude AI</a>
        </p>
    </div>
</div>

<style>
.devhold-status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 15px 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.devhold-status {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-left: 10px;
}

.devhold-status.active {
    background: #4CAF50;
    color: #fff;
}

.devhold-status.inactive {
    background: #f44336;
    color: #fff;
}

.devhold-tabs {
    margin-top: 20px;
}

.devhold-tab-nav {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    border-bottom: 1px solid #ccd0d4;
}

.devhold-tab-nav li {
    margin: 0;
}

.devhold-tab-nav a {
    display: block;
    padding: 10px 20px;
    text-decoration: none;
    color: #23282d;
    border: 1px solid transparent;
    border-bottom: none;
    margin-bottom: -1px;
}

.devhold-tab-nav a.active {
    background: #fff;
    border-color: #ccd0d4;
    border-bottom-color: #fff;
}

.devhold-tab-content {
    display: none;
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-top: none;
}

.devhold-tab-content.active {
    display: block;
}

.devhold-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.devhold-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.devhold-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.devhold-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .devhold-slider {
    background-color: #2196F3;
}

input:checked + .devhold-slider:before {
    transform: translateX(26px);
}

.devhold-media-upload {
    display: flex;
    align-items: center;
    gap: 10px;
}

.devhold-design-options {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.devhold-design-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
    position: relative;
}

.devhold-design-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.devhold-design-option input[type="radio"]:checked + .devhold-design-preview {
    border-color: #0073aa;
}

.devhold-design-option:hover {
    border-color: #0073aa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.devhold-design-preview {
    width: 120px;
    height: 80px;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.preview-header {
    font-size: 16px;
    margin-bottom: 4px;
}

.preview-title {
    font-weight: bold;
    font-size: 10px;
    margin-bottom: 2px;
}

.preview-subtitle {
    font-size: 8px;
    opacity: 0.8;
    margin-bottom: 2px;
}

.preview-text {
    font-size: 6px;
    opacity: 0.7;
    text-align: center;
}

.design-name {
    font-weight: 600;
    color: #23282d;
}

.devhold-logo-preview {
    width: 150px;
    height: 150px;
    border: 2px dashed #ccd0d4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    overflow: hidden;
}

.devhold-background-preview {
    width: 200px;
    height: 120px;
    border: 2px dashed #ccd0d4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    overflow: hidden;
    background: #f8f9fa;
}

.devhold-logo-preview img,
.devhold-background-preview img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    display: block;
}

.devhold-social-item {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}

.devhold-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ccd0d4;
    text-align: center;
    color: #666;
}

/* Admin bar stili */
#wpadminbar .devhold-admin-bar-active .ab-icon:before {
    content: "\f308";
    color: #4CAF50;
}

#wpadminbar .devhold-admin-bar-inactive .ab-icon:before {
    content: "\f308";
    color: #f44336;
}
</style>