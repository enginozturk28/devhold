<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $this->options['title'] ); ?></title>
    <meta name="robots" content="noindex, nofollow">
    
    <?php
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
    $this->options = wp_parse_args( $this->options, $default_options );
    
    // Cognify temasının fontlarını kullanalım
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: <?php echo esc_attr( $this->options['background_color'] ?: '#667eea' ); ?>;
            --text-color: <?php echo esc_attr( $this->options['text_color'] ?: '#ffffff' ); ?>;
            --font-heading: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            --font-body: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            <?php if ( ! empty( $this->options['background_image'] ) ) : ?>
            background: url('<?php echo esc_url( $this->options['background_image'] ); ?>') center center / cover no-repeat;
            <?php else : ?>
            background: linear-gradient(135deg, var(--bg-color) 0%, #764ba2 100%);
            <?php endif; ?>
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        <?php if ( ! empty( $this->options['background_image'] ) ) : ?>
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }
        <?php endif; ?>
        
        /* Arka plan animasyonu */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: backgroundMove 60s linear infinite;
            z-index: 0;
        }
        
        @keyframes backgroundMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .maintenance-container {
            position: relative;
            z-index: 1;
            text-align: center;
            <?php if ( $this->options['design_style'] === 'detailed' ) : ?>
            max-width: 800px;
            padding: 3rem 2rem;
            <?php else : ?>
            max-width: 600px;
            padding: 2rem;
            <?php endif; ?>
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .site-logo {
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .site-logo img {
            max-width: 200px;
            height: auto;
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            animation: rotate 4s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        h1 {
            font-family: var(--font-heading);
            <?php if ( $this->options['design_style'] === 'detailed' ) : ?>
            font-size: 3rem;
            <?php else : ?>
            font-size: 2.5rem;
            <?php endif; ?>
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        .maintenance-message {
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .countdown-item {
            background: rgba(255, 255, 255, 0.15);
            border-radius: var(--border-radius);
            padding: 1rem;
            min-width: 80px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }
        
        .countdown-item:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            font-family: var(--font-heading);
        }
        
        .countdown-label {
            font-size: 0.875rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .social-links {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .social-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 2rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            width: 60%;
            animation: progressAnimation 2s ease-in-out infinite;
        }
        
        @keyframes progressAnimation {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        
        .developer-credit {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            opacity: 0.7;
            text-align: right;
        }
        
        .developer-credit a {
            color: var(--text-color);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .developer-credit a:hover {
            opacity: 1;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .maintenance-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .maintenance-message {
                font-size: 1rem;
            }
            
            .countdown-item {
                min-width: 70px;
                padding: 0.75rem;
            }
            
            .countdown-number {
                font-size: 1.5rem;
            }
        }
    </style>
    
    <?php if ( ! empty( $this->options['custom_css'] ) ) : ?>
    <style>
        <?php echo $this->options['custom_css']; ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <div class="maintenance-container">
        <?php if ( ! empty( $this->options['logo_url'] ) ) : ?>
        <div class="site-logo">
            <img src="<?php echo esc_url( $this->options['logo_url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
        </div>
        <?php else : ?>
        <div class="maintenance-icon">🔧</div>
        <?php endif; ?>
        
        <h1><?php echo esc_html( $this->options['subtitle'] ); ?></h1>
        
        <div class="maintenance-message">
            <?php echo wp_kses_post( $this->options['message'] ); ?>
        </div>
        
        <?php if ( ! empty( $this->options['countdown_enabled'] ) && ! empty( $this->options['countdown_date'] ) ) : ?>
        <div class="countdown-container" id="countdown">
            <div class="countdown-item">
                <span class="countdown-number" id="days">0</span>
                <span class="countdown-label"><?php esc_html_e( 'Gün', 'devhold' ); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="hours">0</span>
                <span class="countdown-label"><?php esc_html_e( 'Saat', 'devhold' ); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="minutes">0</span>
                <span class="countdown-label"><?php esc_html_e( 'Dakika', 'devhold' ); ?></span>
            </div>
            <div class="countdown-item">
                <span class="countdown-number" id="seconds">0</span>
                <span class="countdown-label"><?php esc_html_e( 'Saniye', 'devhold' ); ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ( ! empty( $this->options['social_links'] ) ) : ?>
        <div class="social-links">
            <?php foreach ( $this->options['social_links'] as $link ) : ?>
                <?php
                $icon = '';
                switch ( $link['platform'] ) {
                    case 'facebook':
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
                        break;
                    case 'twitter':
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>';
                        break;
                    case 'instagram':
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/></svg>';
                        break;
                    case 'linkedin':
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>';
                        break;
                    case 'youtube':
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
                        break;
                    default:
                        $icon = '<svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M10.9,2.1c-4.6,0.5-8.3,4.2-8.8,8.7c-0.5,4.7,2.2,8.9,6.3,10.5C8.7,21.4,9,21.2,9,20.8v-1.6c0,0-0.4,0.1-0.9,0.1 c-1.4,0-2-1.2-2.1-1.9c-0.1-0.4-0.3-0.7-0.6-1C5.1,16.3,5,16.3,5,16.2C5,16,5.3,16,5.4,16c0.6,0,1.1,0.7,1.3,1c0.5,0.8,1.1,1,1.4,1 c0.4,0,0.7-0.1,0.9-0.2c0.1-0.7,0.4-1.4,1-1.8c-2.3-0.5-4-1.8-4-4c0-1.1,0.5-2.2,1.2-3C7.1,8.8,7,8.3,7,7.6C7,7.2,7,6.6,7.3,6 c0,0,1.4,0,2.8,1.3C10.6,7.1,11.3,7,12,7s1.4,0.1,2,0.3C15.3,6,16.8,6,16.8,6C17,6.6,17,7.2,17,7.6c0,0.8-0.1,1.2-0.2,1.4 c0.7,0.8,1.2,1.8,1.2,3c0,2.2-1.7,3.5-4,4c0.6,0.5,1,1.4,1,2.3v2.6c0,0.3,0.3,0.6,0.7,0.5c3.7-1.5,6.3-5.1,6.3-9.3 C22,6.1,16.9,1.4,10.9,2.1z"/></svg>';
                }
                ?>
                <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" class="social-link" title="<?php echo esc_attr( ucfirst( $link['platform'] ) ); ?>">
                    <?php echo $icon; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>
    
    <div class="developer-credit">
        <?php esc_html_e( 'Geliştirici:', 'devhold' ); ?> <a href="https://enginozturk.tr" target="_blank">Engin ÖZTÜRK & Claude AI</a>
    </div>
    
    <?php if ( ! empty( $this->options['countdown_enabled'] ) && ! empty( $this->options['countdown_date'] ) ) : ?>
    <script>
        // Geri sayım
        const countdownDate = new Date("<?php echo esc_js( $this->options['countdown_date'] ); ?>").getTime();
        
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = countdownDate - now;
            
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerHTML = "<p><?php esc_html_e( 'Süre doldu!', 'devhold' ); ?></p>";
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("days").innerHTML = days;
            document.getElementById("hours").innerHTML = hours;
            document.getElementById("minutes").innerHTML = minutes;
            document.getElementById("seconds").innerHTML = seconds;
        }, 1000);
    </script>
    <?php endif; ?>
</body>
</html>